<?php

namespace Packages\Purchaser\Searchers;

use App\Helpers\Utils;
use Illuminate\Support\Facades\DB;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StoreOperationModel;

/**
 * search by latest products
 */
class PurchaserShortageSearcher
{
    /**
     * search
     * @param $params
     * @return array
     */
    public static function search($params){
        $sql = '
            WITH "purchaserBoy" AS (
                SELECT
                    oi."productId" AS "id",
                    SUM(oi.qty) as "totalQty",
                    SUM(oi.qty * p."unitStep" ) as "totalUnits"

                    FROM orders o
                    
                    INNER JOIN "orderItems" oi ON oi."orderId" = o.id
                    INNER JOIN products p ON p.id = oi."productId"
                    INNER JOIN "deliveryIntervals" di ON di.id = o."deliveryIntervalId"
                    
                    WHERE 1=1
                        %s
                
                    GROUP BY oi."productId"
            ),
            
            "comparisonResidues" AS (
                SELECT 
                    pb."id",
                    pb."totalQty",
                    pb."totalUnits" AS "orderRealUnits",
                    soc."unitId" AS "storeUnitId",
                    SUM(
                        CASE
                             WHEN s."deletedAt" IS NOT NULL OR sp."deletedAt" IS NOT NULL
                             THEN 0
                             ELSE COALESCE(soc."realUnits", 0)
                        END
                    ) AS "storeRealUnits"
                                
                FROM "purchaserBoy" AS pb
            
                LEFT JOIN "storeOperationContents" AS soc
                    ON soc."productId" = pb."id"
                        
                LEFT JOIN "stores" AS s
                    ON s."id" = soc."storeId"
            
                LEFT JOIN "storePlaces" AS sp
                    ON sp."id" = soc."storePlaceId"
                
                LEFT JOIN "products" AS p
                    ON p."id" = pb."id"
                                
                WHERE 1=1
                    AND COALESCE(s."type", \''.StoreModel::TYPE_PRODUCT.'\') = :storeType
                    AND (
                        p."unitId" = soc."unitId" 
                        OR soc."unitId" IS NULL
                    )
                                
                GROUP BY
                    pb."id",
                    soc."unitId",
                    pb."totalQty",
                    pb."totalUnits"
            ),
            
            "productShortage" AS (
                SELECT 
                    cr."id",
                    cr."totalQty",
                    SUM(((cr."storeRealUnits" - cr."orderRealUnits") / p."unitStep") * p."netCostPerStep") AS "total",
                    SUM(cr."storeRealUnits" - cr."orderRealUnits") AS "totalUnits"
                        
                FROM "comparisonResidues" AS cr
                
                JOIN "products" AS p
                    ON p."id" = cr."id"
                
                GROUP BY 
                    cr."id",
                    cr."totalQty"
            )
            
            SELECT 
                ps."id",
                ABS(ps."totalUnits"::INTEGER) AS "totalUnits",
                ps."totalQty"::INTEGER,
                ABS(ps."total"::INTEGER) AS "total",
                
                p."name" AS "productName",
                p."netCostPerStep" AS "netCostPerStep",
                
                g.name as "groupName",
                g.id as "groupId",
                
                u."fullName" AS "unitFullName",
                u."shortName" AS "unitShortName",
                u."shortDerName" AS "unitShortDerName",
                u."factor" AS "unitFactor",
                p."unitStep" AS "unitStep"
                    
            FROM "productShortage" AS ps
            
            JOIN "products" AS p
                ON p."id" = ps."id"
                    
            INNER JOIN "groups" g 
                ON g."id" = p."groupId"
                    
            INNER JOIN "units" u 
                ON u."id" = p."unitId"
            
            WHERE "totalUnits" < 0
        ';

        $binds = [];
        $binds[':storeType'] = StoreModel::TYPE_PRODUCT;
        [$where, $binds] = self::getWhereBinds($params, $binds);
        $sql = sprintf($sql, $where);

        $shortage = DB::select($sql, $binds);

        return $shortage;
    }

    public static function searchGift($params) {
        $sql = '
            WITH "purchaserBoy" AS (
                SELECT
                    g."id",
                    og."bonuses",
                    SUM(og."qty") as "totalQty"
                    
                FROM orders o
                    INNER JOIN "orderGifts" og ON og."orderId" = o.id
                    INNER JOIN gifts g ON g.id = og."giftId"
                    INNER JOIN "deliveryIntervals" di ON di.id = o."deliveryIntervalId"
                    
                WHERE 1=1 
                    %s
           
                GROUP BY 
                    g."id",
                    og."bonuses"
            ),
            
            "comparisonResidues" AS (
                SELECT 
                    pb."id",
                    pb."bonuses",
                    pb."totalQty",
                    SUM(
                        CASE
                             WHEN s."deletedAt" IS NOT NULL OR sp."deletedAt" IS NOT NULL
                             THEN 0
                             ELSE COALESCE(soc."realUnits", 0)
                        END
                    ) AS "storeRealUnits"
                                
                FROM "purchaserBoy" AS pb

                LEFT JOIN "storeOperationContents" AS soc
                    ON soc."giftId" = pb."id"
                        
                LEFT JOIN "stores" AS s
                    ON s."id" = soc."storeId"
            
                LEFT JOIN "storePlaces" AS sp
                    ON sp."id" = soc."storePlaceId"
                
                LEFT JOIN "gifts" AS g
                    ON g."id" = pb."id"
                                
                WHERE 1=1
                    AND COALESCE(s."type", \''.StoreModel::TYPE_GIFT.'\') = :storeType
                                
                GROUP BY
                    pb."id",
                    pb."bonuses",
                    pb."totalQty"
            ),
            
            "productShortage" AS (
                SELECT 
                    cr."id",
                    cr."bonuses",
                    SUM(cr."bonuses" * (cr."storeRealUnits" - cr."totalQty")) AS "totalBonuses",
                    cr."totalQty",
                    SUM(cr."storeRealUnits" - cr."totalQty") AS "total"
                                
                FROM "comparisonResidues" AS cr
                
                JOIN "products" AS p
                    ON p."id" = cr."id"
                
                GROUP BY 
                    cr."id",
                    cr."bonuses",
                    cr."totalQty"
            )
            
            SELECT 
                ps."id",
                ps."bonuses",
                ABS(ps."totalBonuses"::INTEGER) AS "totalBonuses",
                ps."totalQty"::INTEGER,
                ABS(ps."total"::INTEGER) AS "total",
                
                g."name" AS "giftName"
                    
            FROM "productShortage" AS ps
                    
            INNER JOIN "gifts" g 
                ON g."id" = ps."id"
            
            WHERE "total" < 0
        ';

        $binds = [];
        $binds[':storeType'] = StoreModel::TYPE_GIFT;
        [$where, $binds] = self::getWhereBinds($params, $binds);
        $sql = sprintf($sql, $where);

        $shortage = DB::select($sql, $binds);

        return $shortage;
    }

    /**
     * get the condition and parameters for the search
     * @param $params
     * @param $binds
     * @return array
     */
    public static function getWhereBinds($params, $binds = []) {
        $where = '';

        if (boolval($params['date'] ?? false)) {
            $where .= ' AND o."deliveryDate" = :deliveryDate';
            $binds[':deliveryDate'] = locale()->dateToDbStr($params['date']);
        }

        if (boolval($params['orderStatus'] ?? false)) {
            $where .= ' AND o."status" = :orderStatus';
            $binds[':orderStatus'] = $params['orderStatus'];
        }

        if (boolval($params['startHour'] ?? false)) {
            $where .= ' AND di."startHour" >= :startHour';
            $binds[':startHour'] = $params['startHour'];
        }

        if (boolval($params['endHour'] ?? false)) {
            $where .= ' AND di."startHour" <= :endHour';
            $binds[':endHour'] = $params['endHour'];
        }

        return [
            $where,
            $binds,
        ];
    }
}