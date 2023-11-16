<?php

namespace Packages\Store\Searchers;

use App\Components\Paginator;
use App\Helpers\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Packages\Store\Converters\StoreOperationConverter;
use Packages\Store\Converters\StorePlaceConverter;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StoreOperationModel;

/**
 * search content of products in the store
 */
class StoreOperationsSearcher
{
    protected $where;
    protected $binds;

    /**
     * search with listing
     * @param $params
     * @return array
     */
    public function index($params){
        self::setWhereBinds($params);

        $sort = Arr::has($params, 'sort') ? $params['sort'] : StoreOperationModel::SORT_CREATED;
        $sortDirect = Arr::has($params, 'sortDirect') ? $params['sortDirect'] : 'desc';

        $count = self::getCount($params);
        $pager = new Paginator(
            $count,
            $params['perPage'] ?? 25,
            $params['page'] ?? 1
        );
        $limit = $pager->getLimit();
        $offset = $pager->getOffset();

        $sql = '
            SELECT
                so.*,
                CASE 
                    WHEN sof."storeOperationId" IS NULL THEN false
                    ELSE true
                END AS "isFiles"
                
            FROM "storeOperations" AS so
            
            LEFT JOIN "storeOperationFiles" AS sof
                ON sof."storeOperationId" = so."id"
            
            WHERE 1=1
                '.$this->where.'
                
            GROUP BY
                so."id",
                so.*,
                "isFiles"
                
            ORDER BY "'.$sort.'" '.$sortDirect.'
                
            OFFSET '.$offset.'LIMIT '.$limit.'
        ';

        $operations = DB::select($sql, $this->binds);
        $operations = Utils::objectToArray($operations);

        return [
            'operations' => $operations,
            'pager' => $pager->toArray(),
        ];
    }

    /**
     * search with single
     * @param $id
     * @return array|mixed
     */
    public function show($id) {
        $operation = DB::selectOne('
            SELECT
                a."id" AS "adminId",
                a."name" AS "adminName",
                c."id" AS "clientId",
                c."name" AS "clientName",
                
                so.*
            FROM "storeOperations" AS so
            
            LEFT JOIN "admins" AS a 
                ON a."id" = so."adminId"
                
            LEFT JOIN "clients" AS c
                ON c."id" = so."clientId"
            
            WHERE 1=1
              AND so."id" = :id
        ', [
            ':id' => $id,
        ]);
        $operation = Utils::objectToArray($operation);
        $operation = StoreOperationConverter::singleToFront($operation);

        $contents = DB::select('
            SELECT
                gf."id" AS "giftId",
                gf."name" AS "giftName",
                
                p."id" AS "productId",
                p."name" AS "productName",
                
                COALESCE(p."price", gf."price") AS "price",
                
                g."name" AS "groupName",
                g."bgColor" AS "groupBgColor",
                s."name" AS "storeName",
                sp."name" AS "storePlaceName",
                
                soc.*,
                
                u."shortName" AS "unitShortName",
                u."shortDerName" AS "unitShortDerName",
                p."unitStep" AS "unitStep",
                u."factor" AS "unitFactor"
                
            FROM "storeOperationContents" AS soc
            
            LEFT JOIN "products" AS p
                ON p."id" = soc."productId"
                
            LEFT JOIN "gifts" AS gf
                ON gf."id" = soc."giftId"
                
            LEFT JOIN "groups" AS g
                ON g."id" = p."groupId"
                
            JOIN "stores" AS s
                ON s."id" = soc."storeId"    
            
            JOIN "storePlaces" AS sp
                ON sp."id" = soc."storePlaceId"
                
            LEFT JOIN "units" AS u
                ON u."id" = soc."unitId"
            
            WHERE 1=1
                AND s."deletedAt" IS NULL
                AND sp."deletedAt" IS NULL
                AND soc."storeOperationId" = :id
        ', [
            ':id' => $id,
        ]);

        $contents = Utils::objectToArray($contents);
        $contents = StorePlaceConverter::collectionToFront($contents);
        $operation['contents'] = $contents;

        $files = DB::select('
            SELECT * 
            FROM "storeOperationFiles" AS sof 
            WHERE 1=1
                AND sof."storeOperationId" = :id
        ', [
            ':id' => $id,
        ]);

        if($files) {
            $files = Utils::objectToArray($files);
            foreach ($files as &$file) {
                $file['src'] = Storage::url($file['src']);
            }
            $operation['files'] = $files;
        }

        return $operation;
    }

    public static function productOperations($productId) {
        $sql = '
            SELECT
                so."createdAt",
                so."updatedAt",
                soc."netCostPerStep",
                
                u."id" AS "unitId",
                u."shortName" AS "unitShortName",
                u."shortDerName" AS "unitShortDerName",
                u."step" AS "unitStep",
                u."factor" AS "unitFactor"
                
            FROM "storeOperationContents" AS soc
            
            JOIN "storeOperations" AS so 
                ON so."id" = soc."storeOperationId"
            
            JOIN "units" AS u
                ON u."id" = soc."unitId"
                
            JOIN "stores" AS s  
                ON s."id" = soc."storeId"    
            
            JOIN "storePlaces" AS sp
                ON sp."id" = soc."storePlaceId"
            
            WHERE 1=1
                AND s."deletedAt" IS NULL
                AND sp."deletedAt" IS NULL
                AND so."type" = :type
                AND so."status" = :status
                AND soc."productId" = :productId
                AND s."type" = :storeType
            
            OFFSET 0 LIMIT 5
        ';

        $result = DB::select($sql, [
            ':type' => StoreOperationModel::PUT_TYPE,
            ':status' => StoreOperationModel::COMPLETED_STATUS,
            ':productId' => $productId,
            ':storeType' => StoreModel::TYPE_PRODUCT,
        ]);

        return $result;
    }

    public static function productResidue($productId) {
        $sql = '
            SELECT
                "pr".*,
                
                s."name" AS "storeName",
                sp."name" AS "storePlaceName",
                
                u."id" AS "unitId",
                u."shortName" AS "unitShortName",
                u."shortDerName" AS "unitShortDerName",
                u."step" AS "unitStep",
                u."factor" AS "unitFactor"
            FROM (
                SELECT
                    soc."productId",
                    soc."storeId",
                    soc."storePlaceId",
                    soc."unitId",
                    
                    SUM(soc."realUnits") AS "realUnits"
                    
                FROM "storeOperationContents" AS soc
                                    
                WHERE 1=1 
                    AND soc."productId" = :productId
                
                GROUP BY
                    soc."storeId",
                    soc."storePlaceId",
                    soc."productId",
                    soc."unitId"
            ) AS "pr"
            
            JOIN "stores" AS s
                ON s."id" = pr."storeId" 
                
            JOIN "storePlaces" AS sp
                ON sp."id" = pr."storePlaceId" 
            
            JOIN "products" AS p
                ON p."id" = pr."productId" 
                
            JOIN "units" AS u
                ON u."id" = pr."unitId"
                
            WHERE 1=1
                AND s."deletedAt" IS NULL
                AND sp."deletedAt" IS NULL
                AND s."type" = :storeType
            
        ';

        $result = DB::select($sql, [
            ':productId' => $productId,
            ':storeType' => StoreModel::TYPE_PRODUCT
        ]);
        $result = Utils::objectToArray($result);
        $result = StorePlaceConverter::collectionToFront($result);

        return $result;
    }

    public static function giftResidue($giftId) {
        $sql = '
            SELECT
                "pr".*,
                
                s."name" AS "storeName",
                sp."name" AS "storePlaceName"
                
            FROM (
                SELECT
                    soc."giftId",
                    soc."storeId",
                    soc."storePlaceId",
                    
                    SUM(soc."realUnits") AS "realUnits"
                    
                FROM "storeOperationContents" AS soc
                                    
                WHERE 1=1 
                    AND soc."giftId" = :giftId
                
                GROUP BY
                    soc."storeId",
                    soc."storePlaceId",
                    soc."giftId"
            ) AS "pr"
            
            JOIN "stores" AS s
                ON s."id" = pr."storeId" 
                
            JOIN "storePlaces" AS sp
                ON sp."id" = pr."storePlaceId" 
            
            JOIN "gifts" AS g
                ON g."id" = pr."giftId" 
                
                
            WHERE 1=1
                AND s."deletedAt" IS NULL
                AND sp."deletedAt" IS NULL
                AND s."type" = :storeType
        ';

        $result = DB::select($sql, [
            ':giftId' => $giftId,
            ':storeType' => StoreModel::TYPE_GIFT
        ]);
        $result = Utils::objectToArray($result);
        $result = StorePlaceConverter::collectionToFront($result);

        return $result;
    }

    /**
     * set the condition and parameters for the search
     * @param $params
     */
    public function setWhereBinds($params) {
        $this->where = '';
        $this->binds = [];

        if(boolval($params['id'] ?? false)){
            $this->where .= 'AND so."id" = :id'."\n";
            $this->binds[':id'] = $params['id'];
        }

        if(boolval($params['storeId'] ?? false)){
            $this->where .= 'AND :storeId = ANY(so."storeIds")'."\n";
            $this->binds[':storeId'] = $params['storeId'];
        }

        if(boolval($params['storeType'] ?? false)){
            $this->where .= 'AND s."type" = :storeType'."\n";
            $this->binds[':storeType'] = $params['storeType'];
        }

        if(boolval($params['adminId'] ?? false)){
            $this->where .= 'AND so."adminId" = :adminId'."\n";
            $this->binds[':adminId'] = $params['adminId'];
        }

        if(boolval($params['clientId'] ?? false)){
            $this->where .= 'AND so."clientId" = :clientId'."\n";
            $this->binds[':clientId'] = $params['clientId'];
        }

        if(boolval($params['orderId'] ?? false)){
            $this->where .= 'AND so."orderId" = :orderId'."\n";
            $this->binds[':orderId'] = $params['orderId'];
        }

        if(boolval($params['type'] ?? false)){
            $this->where .= 'AND so."type" = :type'."\n";
            $this->binds[':type'] = $params['type'];
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
                COUNT(so.*)
            FROM "storeOperations" AS so
            
            WHERE 1=1
                '.$this->where.'

        ', $this->binds);

        if(!empty($count)) {
            $count = (int)$count[0]->count;
        } else {
            $count = 0;
        }

        return $count;
    }
}