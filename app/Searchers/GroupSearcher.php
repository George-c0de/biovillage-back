<?php

namespace App\Searchers;

use App\Helpers\DbHelper;
use App\Models\AddressModel;
use App\Models\Auth\Client;
use App\Models\GroupModel;
use App\Models\ProductModel;
use App\Service\ClientService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class GroupSearcher extends BaseSearcher {

    const DEFAULT_LIMIT = 100;

    /**
     * @var $id
     */
    public $id;

    /**
     * Filter name by ILIKE
     * @var $clientId
     */
    public $name;

    /**
     * Search only active groups
     * @var $active
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
    public function addIdCondition(&$sql, &$binds) {
        if (!empty($this->id )) {
            $sql .= ' AND g.id = :id';
            $binds[':id'] = $this->id;
        }
    }

    /**
     * Add active addresses
     * @param $sql
     * @param $binds
     */
    public function addActiveCondition(&$sql, &$binds) {
        if(boolval($this->onlyActive)) {
            $sql .= ' AND g.active = true';
        }
    }

    /**
     * Add name conditions
     * @param $sql
     * @param $binds
     */
    public function addNameCondition(&$sql, &$binds) {
        if(!empty($this->name)) {
            $sql .= ' AND g.name ILIKE :name';
            $binds[':name'] = '%' . $this->name . '%';
        }
    }


    /**
     * Add sort
     * @param $sql
     */
    public function addSort(&$sql) {
        if(empty($this->sort)) {
            $this->sort = GroupModel::SORT_ORDER;
        }
        if(empty($this->sortDirect)) {
            $this->sortDirect =  'ASC';
        }
        $sortSqlFmt = [
            GroupModel::SORT_CREATED => 'g."createdAt" %s, g.name',
            GroupModel::SORT_NAME => 'g.name %s',
            GroupModel::SORT_ORDER => 'g.order %s, g.name'
        ][$this->sort] ?? 'g.order %s, g.name';

        $sql .=  sprintf(' ORDER BY ' . $sortSqlFmt, $this->sortDirect);
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
        $this->addIdCondition($sql, $binds);
        $this->addActiveCondition($sql, $binds);
        $this->addNameCondition($sql, $binds);
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
        $sqlCore = '
            WITH
                target_groups AS (
                    SELECT g.*
                    FROM groups g
                    WHERE g."deletedAt" IS NULL %s
                ),
                group_images AS (
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
                    FROM target_groups g
                        LEFT JOIN images i ON i."groupName" = :imageGroupName
                            AND i."entityId" = g.id
                    WHERE i."deletedAt" IS NULL AND i.id IS NOT NULL
                    GROUP BY g.id
                ),
                groups_tmp_tags AS (
                    SELECT
                        "groupId",
                        ARRAY_AGG(tag) as tags
                    FROM (
                        SELECT DISTINCT
                            (unnest(tags)) as tag,
                            "groupId"
                        FROM products p
                            INNER JOIN target_groups tg ON tg.id = p."groupId"
                    ) gt
                    GROUP BY gt."groupId"
                ),
                groups_full_tags AS (
                    SELECT
                        gtt."groupId",
                        ARRAY_AGG(ARRAY[ t.id::text, t.name::text ]) as tags
                    FROM groups_tmp_tags gtt
                        LEFT JOIN tags t ON ARRAY[t.id::int] && gtt.tags
                    WHERE t.id IS NOT NULL AND t."deletedAt" IS NULL
                    GROUP BY gtt."groupId"
                )
            SELECT
                tg.*,
                gi.images[1][2] as "imageSrc", -- first image url
                gft.tags
            FROM target_groups tg
                LEFT JOIN groups_full_tags gft ON gft."groupId" = tg.id
                LEFT JOIN group_images gi ON gi.id = tg.id
        ';
        $binds[':imageGroupName'] = GroupModel::IMAGE_GROUP_NAME;

        // Conditions, sort and limit
        $sqlConditions = '';
        $this->addAllConditions($sqlConditions, $binds);

        $this->addSort($sqlConditions);
        $this->addLimitOffset($sqlConditions, $binds);

        // Prepare result
        return array_map(function($row) {
            $row = (array)$row;
            $row['createdAt'] = locale()->dbDtToDtStr($row['createdAt']);
            $row['updatedAt'] = locale()->dbDtToDtStr($row['createdAt']);
            $row['tags'] = DbHelper::pgArrayToArray($row['tags']);
            return $row;
        }, DB::select(sprintf($sqlCore, $sqlConditions), $binds));
    }

}