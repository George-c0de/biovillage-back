<?php

namespace Packages\Store\Searchers;

use App\Helpers\Utils;
use Illuminate\Support\Facades\DB;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StoreOperationModel;

/**
 * search by latest products
 */
class LastGiftsSearcher
{
    /**
     * search
     * @param $params
     * @return array
     */
    public static function search($params){
        $sql = '
            SELECT
                DISTINCT ON (soc."giftId") "giftId",
                g."price" AS "netCostPerStep",
                soc."storeId",
                s."systemPlaceId" AS "storePlaceId"
                
            FROM "storeOperationContents" AS soc
            
            JOIN "storeOperations" AS so
                ON so."id" = soc."storeOperationId"

            LEFT JOIN "products" AS p
                ON p."id" = soc."productId" 
                
            JOIN "stores" AS s
                ON s."id" = soc."storeId"
                
            JOIN "storePlaces" AS sp
	            ON sp."id" = soc."storePlaceId"
	            
            JOIN "gifts" AS g
	            ON g."id" = soc."giftId"
            
            WHERE 1=1
                AND s."deletedAt" IS NULL
                AND sp."deletedAt" IS NULL
                AND so."type" = \''.StoreOperationModel::PUT_TYPE.'\'
                AND so."status" = \''.StoreOperationModel::COMPLETED_STATUS.'\'
                AND sp."isSystem" = FALSE
                %s
                    
            ORDER BY soc."giftId" ASC, so."createdAt" DESC
        ';

        $binds[':storeType'] = StoreModel::TYPE_GIFT;
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
    public static function getWhereBinds($params, $binds = []) {
        $where = '';

        if(boolval($params['giftIds'] ?? false)){
            $inValues = [];
            foreach ($params['giftIds'] as $k => $id){
                $key = ':'.$k.'giftIds';
                $inValues[] = $key;
                $binds[$key] = $id;
            }

            $inValues = implode(' ,', $inValues);
            $where .= ' AND soc."giftId" IN('.$inValues.') ';
        }

        if(boolval($params['storeId'] ?? false)){
            $where .= ' AND soc."storeId" = :storeId ';
            $binds[':storeId'] = $params['storeId'];
        }

        return [
            $where,
            $binds,
        ];
    }
}