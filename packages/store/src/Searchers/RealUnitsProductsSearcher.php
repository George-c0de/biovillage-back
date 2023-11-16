<?php

namespace Packages\Store\Searchers;

use App\Helpers\Utils;
use Illuminate\Support\Facades\DB;

/**
 * search for the real units of products
 */
class RealUnitsProductsSearcher
{
    /**
     * search
     * @param $params
     * @return array
     */
    public static function search($params, $notZero = true){
        $sql = '
            WITH "realUnits" AS (
                SELECT
                    soc."productId",
                    soc."storeId",
                    soc."storePlaceId",
                    soc."netCostPerStep",
                    soc."unitId",
                    p."unitId" AS "unitIdBase",
                    p."unitStep",
                    SUM(soc."realUnits") AS "realUnits"
                    
                FROM "storeOperationContents" AS soc
                
                LEFT JOIN "stores" AS s
                    ON s."id" = soc."storeId" 
                
                LEFT JOIN "storePlaces" AS sp
                    ON sp."id" = soc."storePlaceId" 
                
                LEFT JOIN "products" AS p
                    ON p."id" = soc."productId" 
                            
                WHERE 1=1 
                    AND s."deletedAt" IS NULL
                    AND sp."deletedAt" IS NULL
                    %s
                
                GROUP BY
                    s."order",
                    sp."order",
                    soc."storeId",
                    soc."productId",
                    soc."storePlaceId",
                    soc."netCostPerStep",
                    soc."unitId",
                    p."unitId",
                    p."unitStep"
                    
                ORDER BY
                    s."order" ASC,
                    sp."order" ASC,
                    soc."netCostPerStep" ASC
            )
            
            SELECT
                ru."productId",
                ru."storeId",
                ru."storePlaceId",
                ru."netCostPerStep",
                ru."unitId",
                ru."unitIdBase",
                ru."realUnits",
                SUM(ABS(ru."realUnits") * ru."netCostPerStep" / ru."unitStep") AS "netCost"
                
            FROM "realUnits" AS ru
            
            GROUP BY
                ru."productId",
                ru."storeId",
                ru."storePlaceId",
                ru."netCostPerStep",
                ru."unitId",
                ru."unitIdBase",
                ru."unitStep",
                ru."realUnits"
        ';

        if($notZero){
            $sql = '
                SELECT * FROM (
                    '.$sql.'
                ) AS "ru"
                WHERE ru."realUnits" != 0
            ';
        }

        [$where, $binds] = self::getWhereBinds($params);
        $sql = sprintf($sql, $where);

        $realUnitsProducts = DB::select($sql, $binds);
        $realUnitsProducts = Utils::objectToArray($realUnitsProducts);

        return $realUnitsProducts;
    }

    /**
     * searchQuick
     * @param $params
     * @return array
     */
    public static function searchQuick($params){
        $sql = '
            SELECT * 
            FROM (
                SELECT
                    soc."productId" AS "id",
                    SUM(soc."realUnits") AS "realUnits"
                    
                FROM "storeOperationContents" AS soc
                
                LEFT JOIN "stores" AS s
                    ON s."id" = soc."storeId" 
                
                LEFT JOIN "storePlaces" AS sp
                    ON sp."id" = soc."storePlaceId" 
                
                LEFT JOIN "products" AS p
                    ON p."id" = soc."productId" 
                            
                WHERE 1=1 
                    AND s."deletedAt" IS NULL
                    AND sp."deletedAt" IS NULL
                    AND soc."unitId" = p."unitId"
                    %s
                
                GROUP BY
                    soc."productId"
                    
            ) AS "ru"
            WHERE ru."realUnits" != 0
        ';

        [$where, $binds] = self::getWhereBinds($params);
        $sql = sprintf($sql, $where);

        $realUnitsProducts = DB::select($sql, $binds);
        $realUnitsProducts = Utils::objectToArray($realUnitsProducts);

        return $realUnitsProducts;
    }

    /**
     * get the condition and parameters for the search
     * @param $params
     * @return array
     */
    public static function getWhereBinds($params) {
        $where = '';
        $binds = [];

        if(array_key_exists('isSystem', $params) && is_bool($params['isSystem'])){
            $where .= ' AND sp."isSystem" = :isSystem ';
            $binds[':isSystem'] = $params['isSystem'];
        }

        if(boolval($params['storeId'] ?? false)){
            $where .= ' AND soc."storeId" = :storeId ';
            $binds[':storeId'] = $params['storeId'];
        }

        if(boolval($params['storeType'] ?? false)){
            $where .= ' AND s."type" = :storeType ';
            $binds[':storeType'] = $params['storeType'];
        }

        if(boolval($params['notStoreOperationId'] ?? false)){
            $where .= ' AND soc."storeOperationId" != :notStoreOperationId ';
            $binds[':notStoreOperationId'] = $params['notStoreOperationId'];
        }

        if(boolval($params['productIds'] ?? false)){
            $inValues = [];
            foreach ($params['productIds'] as $k => $id){
                $key = ':'.$k.'productIds';
                $inValues[] = $key;
                $binds[$key] = $id;
            }

            $inValues = implode(' ,', $inValues);
            $where .= ' AND soc."productId" IN('.$inValues.')';
        }

        if(boolval($params['storePlaceIds'] ?? false)){
            $inValues = [];
            foreach ($params['storePlaceIds'] as $k => $id){
                $key = ':'.$k.'storePlaceId';
                $inValues[] = $key;
                $binds[$key] = $id;
            }

            $inValues = implode(' ,', $inValues);
            $where .= ' AND sp."id" IN('.$inValues.')';
        }

        return [
            $where,
            $binds,
        ];
    }
}