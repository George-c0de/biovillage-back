<?php

namespace App\Searchers;

use App\Helpers\DbHelper;
use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ProductSearcher extends BaseSearcher {

    const DEFAULT_LIMIT = 1000; // Hard limit if not present

    /**
     * @var $id
     */
    public $id;

    /**
     * @var $ids - Массив ид. продуктов
     */
    public $ids;

    /**
     * @var $groupId
     */
    public $groupId;

    /**
     * @var $onlyPromotion
     */
    public $onlyPromotion;

    /**
     * @var $onlyActive
     */
    public $onlyActive;

    /**
     * @var $tags
     */
    public $tags;

    /**
     * @var $name
     */
    public $name;

    /**
     * Sort field. See SORT_* consts
     * @var $sort
     */
    public $sort;

    /**
     * Direction. ASC | DESC
     * @var $sortDirect
     */
    public $sortDirect;

    /**
     * @var $limit
     */
    public $limit;

    /**
     * @var $offset
     */
    public $offset;

    /**
     * Add id conditions
     * @param $sql
     * @param $binds
     */
    public function addProductIdCond(&$sql, &$binds) {
        if (!empty($this->id )) {
            $sql .= ' AND p.id = :productId';
            $binds[':productId'] = $this->id;
        }
    }

    /**
     * Add product ids conditions
     * @param $sql
     * @param $binds
     */
    public function addProductIdsCond(&$sql) {
        if (!empty($this->ids )) {
            $sql .= ' AND p.id IN (' . implode(', ', $this->ids) . ')';
        }
    }

    /**
     * Add group conditions
     * @param $sql
     * @param $binds
     */
    public function addGroupId(&$sql, &$binds) {
        if (!empty($this->groupId)) {
            $sql .= ' AND p."groupId" = :groupId';
            $binds[':groupId'] = $this->groupId;
        }
    }

    /**
     * Add search only promotion products
     * @param $sql
     * @param $binds
     */
    public function addOnlyPromoConditions(&$sql, &$binds) {
        if(boolval($this->onlyPromotion)) {
            $sql .= ' AND p.promotion IS NOT NULL AND p.promotion != :noPromo';
            $binds[':noPromo'] = ProductModel::PROMO_NO;
        }
    }

    /**
     * Activity condition for products
     * @param $sql
     * @param $binds
     */
    public function addActiveProducts(&$sql, &$binds) {
        if(isset($this->onlyActive)) {
            $sql .= ' AND p.active = :onlyActive';
            $binds['onlyActive'] = $this->onlyActive;
        }
    }

    /**
     * Activity condition for groups
     * @param $sql
     * @param $binds
     */
    public function addActiveGroups(&$sql, &$binds) {
        if(boolval($this->onlyActive)) {
            $sql .= ' AND g.active = true';
        }
    }

    /**
     * Add tags conditions
     * @param $sql
     */
    public function addTags(&$sql) {
        if(!empty($this->tags)) {
            $sql .= sprintf(
                ' AND p.tags && \'%s\'',
                DbHelper::arrayToPgArray($this->tags)
            );
        }
    }

    /**
     * Add name conditions
     * @param $sql
     * @param $binds
     */
    public function addNameConditions(&$sql, &$binds) {
        if(!empty($this->name)) {
            $sql .= ' AND ( p.name ILIKE :name OR p.name % :trgmName )';
            $name = str_replace('%', '', $this->name);
            $binds['name'] = '%' . $name . '%';
            $binds['trgmName'] = $name;
        }
    }

    /**
     * Deleted condition
     * @param $sql
     */
    public function addNotDeletedConditions(&$sql) {
        $sql .= ' AND p."deletedAt" IS NULL';
        $sql .= ' AND g."deletedAt" IS NULL';
    }

    /**
     * Add sort
     * @param $sql
     * @param $binds
     */
    public function addSort(&$sql, &$binds) {
        if(empty($this->sort)) {
            $this->sort = ProductModel::SORT_ORDER;
        }
        if(empty($this->sortDirect)) {
            $this->sortDirect =  'ASC';
        }

        if($this->sort == ProductModel::SORT_SIM && !$this->name){
            $this->sort = ProductModel::SORT_ORDER;
        }

        $sortSqlFmt = [
            ProductModel::SORT_ORDER => 'p.order %s, p.name',
            ProductModel::SORT_NAME => 'p.name %s, p.order',
            ProductModel::SORT_SIM => 'similarity(p.name, :simname) %s',
        ][$this->sort] ?? 'p.order %s';
        $sortSqlFmt = ' ORDER BY ' . $sortSqlFmt;

        if($this->sort == ProductModel::SORT_SIM && $this->name){
            $binds[':simname'] = $this->name;
        }

        $sql .=  sprintf($sortSqlFmt, $this->sortDirect);
    }

    /**
     * Add limit and offset for sql request
     * @param $sql
     * @param $binds
     */
    public function addLimitOffset(&$sql, &$binds) {
        if(empty($this->limit)) {
            $this->limit = self::DEFAULT_LIMIT;
        }
        if(empty($this->offset)) {
            $this->offset = 0;
        }

        $sql .= ' LIMIT :limit OFFSET :offset';
        $binds[':limit'] = $this->limit;
        $binds[':offset'] = $this->offset;
    }

    /**
     * Add all conditions to where clouse
     * @param $sql
     * @param $binds
     */
    public function addAllConditions(&$sql, &$binds) {
        $this->addNotDeletedConditions($sql);
        $this->addProductIdCond($sql, $binds);
        $this->addProductIdsCond($sql);
        $this->addActiveProducts($sql, $binds);
        $this->addActiveGroups($sql, $binds);
        $this->addGroupId($sql, $binds);
        $this->addNameConditions($sql, $binds);
        $this->addOnlyPromoConditions($sql, $binds);
        $this->addTags($sql);
    }


    /**
     * Search products
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    public function search(array $params = [])
    {
        $this->clear();
        $this->load($params);

        //
        $binds = [];
        $sqlTemplate = '
            WITH
                prods AS (
                    SELECT
                        p.id,
                        p.active,
                        p.name,
                        p.order,
                        p."unitId",
                        u."fullName" as "unitFullName",
                        u."shortName" as "unitShortName",
                        u."shortDerName" as "unitShortDerName",
                        p."unitStep",
                        p."unitStep" as "unitMin",
                        u."factor" as "unitFactor",
                        p.price,
                        COALESCE(p.promotion, :defPromo) as promotion,
                        p."groupId",
                        g.name as "groupName",
                        p.tags as _tags,
                        p.description,
                        p.composition,
                        p."shelfLife",
                        p.nutrition,
                        p."netCostPerStep",
                        p."realUnits"
                    FROM products p
                        INNER JOIN units u ON u.id = p."unitId"
                        INNER JOIN groups g ON g.id = p."groupId"
                    WHERE 1=1 %s
                ),
                prod_tags AS (
                    SELECT
                        p.id,
                        ARRAY_AGG(ARRAY[ t.id::text, t.name::text ]) as tags
                    FROM prods p
                        LEFT JOIN tags t ON ARRAY[t.id::int] && p._tags
                    WHERE t.id IS NOT NULL AND t."deletedAt" IS NULL
                    GROUP BY p.id
                ),
                prod_images AS (
                    SELECT
                        p.id,
                        ARRAY_AGG(
                          ARRAY[ 
                              i.id::text,
                              i.src::text,
                              COALESCE( i."srcThumb"::text, \'\'), 
                              i."order"::text 
                          ]
                          ORDER BY i."order"
                        ) as images
                    FROM prods p
                        LEFT JOIN images i ON i."groupName" = :imageGroupName 
                            AND i."entityId" = p.id
                    WHERE i."deletedAt" IS NULL AND i.id IS NOT NULL
                    GROUP BY p.id
                ),
                prod_certs AS (
                    SELECT
                        p.id,
                        ARRAY_AGG(
                            ARRAY[
                                i.id::text,
                                i.src::text,
                                COALESCE( i."srcThumb"::text, \'\'),
                                i."order"::text
                            ]
                            ORDER BY i."order"
                        ) as certificates
                    FROM prods p
                        LEFT JOIN images i ON i."groupName" = :certsGroupName 
                            AND i."entityId" = p.id
                    WHERE i."deletedAt" IS NULL AND i.id IS NOT NULL
                    GROUP BY p.id
                )
            SELECT
                p.*,
                pt.tags,
                pi.images,
                pc.certificates
            FROM prods p
                LEFT JOIN prod_tags pt ON pt.id = p.id
                LEFT JOIN prod_images pi ON pi.id = p.id
                LEFT JOIN prod_certs pc ON pc.id = p.id
        ';

        // Conditions, sort and limit
        $sqlCore = '';
        $this->addAllConditions($sqlCore, $binds);

        // Addition binds
        $binds[':imageGroupName'] = ProductModel::IMAGES_GROUP_NAME;
        $binds[':certsGroupName'] = ProductModel::CERTIFICATES_GROUP_NAME;
        $binds[':defPromo'] = ProductModel::PROMO_NO;

        $this->addSort($sqlTemplate, $binds);
        $this->addLimitOffset($sqlCore, $binds);

        // Make full sql query
        $sql = sprintf($sqlTemplate, $sqlCore);

        // Prepare result
        return array_map(function($row) {
            $row = (array)$row;
            unset($row['_tags']);
            $row['tags'] = DbHelper::pgArrayToArray($row['tags']) ?? [];
            $row['certificates'] = DbHelper::pgArrayToArray($row['certificates']) ?? [];
            $row['images'] = DbHelper::pgArrayToArray($row['images']) ?? [];
            return $row;
        }, DB::select($sql, $binds));
    }


    /**
     * Calc count rows
     * @param $params
     * @return int
     * @throws \ReflectionException
     */
    public function count($params) {
        $this->clear();
        $this->load($params);
        $binds = [];
        $sql = '
            SELECT COUNT(p.id) as cnt
            FROM products p
                INNER JOIN groups g ON g.id = p."groupId"
            WHERE 1=1
        ';
        $this->addAllConditions($sql,$binds );
        $res = DB::select($sql, $binds);
        if(!empty($res)) {
            return (int)$res[0]->cnt;
        }
        return 0;
    }
}