<?php

namespace App\Searchers;

use App\Helpers\DbHelper;
use App\Models\AddressModel;
use App\Models\Auth\Client;
use App\Models\ProductModel;
use App\Service\ClientService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class AddressSearcher extends BaseSearcher {

    const DEFAULT_LIMIT = 10;

    /**
     * @var $id
     */
    public $id;

    /**
     * Filter by clientId
     * @var $clientId
     */
    public $clientId;

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
     * Add client id conditions
     * @param $sql
     * @param $binds
     */
    public function addClientIdCondition(&$sql, &$binds) {
        if (!empty($this->clientId )) {
            $sql .= ' AND a."clientId" = :clientId';
            $binds[':clientId'] = $this->clientId;
        }
    }

    /**
     * Add id conditions
     * @param $sql
     * @param $binds
     */
    public function addIdCondition(&$sql, &$binds) {
        if (!empty($this->id )) {
            $sql .= ' AND a.id = :id';
            $binds[':id'] = $this->id;
        }
    }

    /**
     * Add active addresses
     * @param $sql
     * @param $binds
     */
    public function addActiveCondition(&$sql, &$binds) {
        $sql .= ' AND a."deletedAt" IS NULL';
    }


    /**
     * Add sort
     * @param $sql
     */
    public function addSort(&$sql) {
        if(empty($this->sort)) {
            $this->sort = AddressModel::SORT_ADD;
        }
        if(empty($this->sortDirect)) {
            $this->sortDirect =  'ASC';
        }
        $sortSqlFmt = [
            AddressModel::SORT_ADD => 'a."createdAt"',
            AddressModel::SORT_NAME => 'a.name',
        ][$this->sort] ?? 'a."createdAt';
        $sql .=  ' ORDER BY ' . $sortSqlFmt . ' ' . $this->sortDirect;
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
        $this->addClientIdCondition($sql, $binds);
        $this->addActiveCondition($sql, $binds);
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
            SELECT * FROM (
                SELECT
                    a.id,
                    a."clientId",
                    a.city,
                    a.street,
                    a.house,
                    a.building,
                    a.entrance,
                    a.floor,
                    a.doorphone,
                    a.appt,
                    a.lon::float,
                    a.lat::float,
                    a.comment,
                    a.name,
                    a."createdAt",
                    da.id as "daId",
                    da.name as "daName",
                    da.price as "daPrice",
                    da.color as "daColor",
                    da."deliveryFreeSum" as "daFreeSum",
                    da.id IS NOT NULL as "isDelivery",
                    rank() OVER (PARTITION BY a.id ORDER BY area(path(da."polygon"))) as pos
                FROM addresses a
                    LEFT JOIN "deliveryArea" da ON da."deletedAt" IS NULL AND
                        da."polygon" @> point( a.lat::float, a.lon::float )
                WHERE 1=1 %s
            ) a
            WHERE pos = 1
        ';

        // Conditions, sort and limit
        $sqlConditions = '';
        $this->addAllConditions($sqlConditions, $binds);

        $this->addSort($sqlConditions);
        $this->addLimitOffset($sqlConditions, $binds);

        // Prepare result
        return array_map(function($row) {
            $row = (array)$row;
            $row['createdAt'] = locale()->dbDtToDtStr($row['createdAt']);
            $row['lat'] = floatval($row['lat']);
            $row['lon'] = floatval($row['lon']);
            unset($row['pos']);
            return $row;
        }, DB::select(sprintf($sqlCore, $sqlConditions), $binds));
    }

}