<?php

namespace App\Searchers;

use App\Helpers\DbHelper;
use App\Models\GiftModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class GiftSearcher extends BaseSearcher {
    
    /**
     * @var $id
     */
    public $id;

    /**
     * @var $ids - Array of gift ids
     */
    public $ids;
    
    /**
     * @var $onlyActive
     */
    public $onlyActive;

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
     * Add id conditions
     * @param $sql
     * @param $binds
     */
    public function addGiftIdCond(&$sql, &$binds) {
        if (!empty($this->id )) {
            $sql .= ' AND g.id = :id';
            $binds[':id'] = $this->id;
        }
    }

    /**
     * Add ids conditions
     * @param $sql
     * @param $binds
     */
    public function addGiftIdsCond(&$sql) {
        if (!empty($this->ids)) {
            $sql .= ' AND g.id IN (' . implode(',', $this->ids) . ')';
        }
    }


    /**
     * Activity condition
     * @param $sql
     * @param $binds
     */
    public function addActiveGifts(&$sql) {
        if(boolval($this->onlyActive)) {
            $sql .= ' AND g.active = true';
        }
    }

    /**
     * Deleted condition
     * @param $sql
     */
    public function addNotDeletedConditions(&$sql) {
        $sql .= ' AND g."deletedAt" IS NULL';
    }

    /**
     * Add sort
     * @param $sql
     */
    public function addSort(&$sql) {
        if(empty($this->sort)) {
            $this->sort = GiftModel::SORT_ORDER;
        }
        if(empty($this->sortDirect)) {
            $this->sortDirect =  'ASC';
        }

        $sortSqlFmt = [
            GiftModel::SORT_ORDER => 'g.order %s, g.name',
            GiftModel::SORT_NAME => 'g.name %s, g.order',
        ][$this->sort] ?? 'g.order %s';
        $sortSqlFmt = ' ORDER BY ' . $sortSqlFmt;

        $sql .=  sprintf($sortSqlFmt, $this->sortDirect);
    }

    /**
     * Add all conditions to where clouse
     * @param $sql
     * @param $binds
     */
    public function addAllConditions(&$sql, &$binds) {
        $this->addNotDeletedConditions($sql);
        $this->addGiftIdCond($sql, $binds);
        $this->addGiftIdsCond($sql);
        $this->addActiveGifts($sql);
    }


    /**
     * Search Gifts
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
                gifts_df AS (
                    SELECT
                        g.id,
                        g.active,
                        g.name,
                        g.order,
                        g.price,
                        g.description,
                        g.composition,
                        g."shelfLife"
                    FROM gifts g
                    WHERE 1=1 %s
                ),
                gift_images AS (
                    SELECT
                        g.id,
                        ARRAY_AGG(
                          ARRAY[ 
                              i.id::text,
                              i.src::text,
                              COALESCE( i."srcThumb"::text, \'\'), 
                              i."order"::text 
                          ]
                          ORDER BY i."order"
                        ) as images
                    FROM gifts_df g
                        LEFT JOIN images i ON i."groupName" = :imageGroupName 
                            AND i."entityId" = g.id
                    WHERE i."deletedAt" IS NULL AND i.id IS NOT NULL
                    GROUP BY g.id
                )
            SELECT
                g.*,
                gi.images
            FROM gifts_df g
                LEFT JOIN gift_images gi ON gi.id = g.id
        ';

        // Conditions, sort and limit
        $sqlCore = '';
        $this->addAllConditions($sqlCore, $binds);

        // Addition binds
        $binds[':imageGroupName'] = GiftModel::IMAGES_GROUP_NAME;

        $this->addSort($sqlCore);

        // Make full sql query
        $sql = sprintf($sqlTemplate, $sqlCore);

        // Prepare result
        return array_map(function($row) {
            $row = (array)$row;
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
            SELECT COUNT(g.id) as cnt
            FROM gifts g
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