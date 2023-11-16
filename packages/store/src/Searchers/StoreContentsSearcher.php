<?php

namespace Packages\Store\Searchers;

use App\Components\Paginator;
use App\Helpers\Utils;
use Illuminate\Support\Facades\DB;
use Packages\Store\Converters\StorePlaceConverter;

/**
 * search content of products in the store
 */
class StoreContentsSearcher
{
    protected $join = '
        JOIN "storeOperations" AS so
            ON so."id" = soc."storeOperationId"
        
        LEFT JOIN "products" AS p 
            ON p."id" = soc."productId" 
            
        JOIN "stores" AS s
            ON s."id" = soc."storeId" 
        
        JOIN "storePlaces" AS sp
            ON sp."id" = soc."storePlaceId" 
            
        LEFT JOIN "units" AS u
            ON u."id" = soc."unitId" 
						
        LEFT JOIN "gifts" AS g
            ON g."id" = soc."giftId" 
    ';

    protected $groupBy = '
        GROUP BY
            g."id",
            g."name",
            sp."order",
            soc."productId",
            soc."giftId",
            p."name",
            soc."storePlaceId",
            sp."name",
            soc."unitId",
            u."shortName",
            u."step",
            u."shortDerName",
            u."factor"
    ';

    protected $where = '';
    protected $binds = [];

    /**
     * search
     * @param $id
     * @param $params
     * @return array
     */
    public function search($id, $params){
        self::setWhereBinds($id, $params);

        $count = self::getCount($params);
        $pager = new Paginator(
            $count,
            $params['perPage'] ?? 25,
            $params['page'] ?? 1
        );
        $limit = $pager->getLimit();
        $offset = $pager->getOffset();

        $sql = '
            SELECT * FROM (
                SELECT
                    g."id" AS "giftId",
                    g."name" AS "giftName",
                    soc."productId",
                    p."name" AS "productName",
                    soc."storePlaceId",
                    sp."name" AS "storePlaceName",
                    soc."unitId",
                    u."shortName" AS "unitShortName",
                    u."step" AS "unitStep",
                    u."shortDerName" AS "unitShortDerName",
                    u."factor" AS "unitFactor",
                    SUM(soc."realUnits") AS "realUnits"
                    
                FROM "storeOperationContents" AS soc
                
                '.$this->join.'
                
                WHERE 1=1
                    AND s."deletedAt" IS NULL
                    AND sp."deletedAt" IS NULL
                    '.$this->where.'
                
                '.$this->groupBy.'
                    
                OFFSET '.$offset.'LIMIT '.$limit.'
            ) AS h
            
            WHERE h."realUnits" != 0
        ';

        $contents = DB::select($sql, $this->binds);
        $contents = Utils::objectToArray($contents);
        $contents = StorePlaceConverter::collectionToFront($contents);

        return [
            'contents' => $contents,
            'pager' => $pager->toArray(),
        ];
    }

    /**
     * set the condition and parameters for the search
     * @param $id
     * @param $params
     */
    public function setWhereBinds($id, $params) {
        $this->where = "\n".'AND soc."storeId" = :storeId'."\n";
        $this->binds = [':storeId' => $id];

        if(boolval($params['productId'] ?? false)){
            $this->where .= 'AND soc."productId" = :productId'."\n";
            $this->binds[':productId'] = $params['productId'];
        }

        if(boolval($params['storeType'] ?? false)){
            $this->where .= 'AND s."type" = :storeType'."\n";
            $this->binds[':storeType'] = $params['storeType'];
        }

        if(boolval($params['orderId'] ?? false)){
            $this->where .= 'AND so."orderId" = :orderId'."\n";
            $this->binds[':orderId'] = $params['orderId'];
        }

        if(boolval($params['createdAtBegin'] ?? false)){
            $this->where .= 'AND so."createdAt" >= :createdAtBegin'."\n";
            $this->binds[':createdAtBegin'] = locale()->dateToDbStr($params['createdAtBegin']);
        }

        if(boolval($params['createdAtEnd'] ?? false)){
            $this->where .= 'AND so."createdAt" <= :createdAtEnd'."\n";
            $this->binds[':createdAtEnd'] = locale()->dateToDbStr($params['createdAtEnd']);
        }

        if(boolval($params['updatedAtBegin'] ?? false)){
            $this->where .= 'AND so."updatedAt" >= :updatedAtBegin'."\n";
            $this->binds[':updatedAtBegin'] = locale()->dateToDbStr($params['updatedAtBegin']);
        }

        if(boolval($params['updatedAtEnd'] ?? false)){
            $this->where .= 'AND so."updatedAt" <= :updatedAtEnd'."\n";
            $this->binds[':updatedAtEnd'] = locale()->dateToDbStr($params['updatedAtEnd']);
        }
    }

    /**
     * get count of records
     * @param $params
     * @return array|int
     */
    public function getCount($params) {
        $count = DB::select('
            SELECT
                COUNT(h."entityId") AS count
            FROM (
                SELECT 
                    COALESCE (soc."productId", soc."giftId") AS "entityId",
                    SUM(soc."realUnits") AS "realUnits"
                    
                FROM "storeOperationContents" AS soc
                
                ' . $this->join . '
                
                WHERE 1=1
                    AND s."deletedAt" IS NULL
                    AND sp."deletedAt" IS NULL
                    ' . $this->where . '
                
                ' . $this->groupBy . '
            ) AS h
                
            WHERE h."realUnits" != 0
        ', $this->binds);

        if(!empty($count)) {
            $count = (int)$count[0]->count;
        } else {
            $count = 0;
        }

        return $count;
    }
}