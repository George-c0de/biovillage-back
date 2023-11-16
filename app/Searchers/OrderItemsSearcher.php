<?php

namespace App\Searchers;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\AddressModel;
use App\Models\Auth\Client;
use App\Models\GiftModel;
use App\Models\GroupModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Service\ClientService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OrderItemsSearcher extends BaseSearcher {

    /**
     * @var $id
     */
    public $id;

    /**
     * @var $ids - Array of ids
     */
    public $ids;

    /**
     * @var $orderId
     */
    public $orderId;

    /**
     * @var $onlyActive
     */
    public $onlyActive;

    /**
     * Add order id conditions
     * @param $sql
     * @param $binds
     */
    public function addOrderIdCondition(&$sql, &$binds) {
        if (!empty($this->orderId )) {
            $sql .= ' AND oi."orderId" = :orderId';
            $binds[':orderId'] = $this->orderId;
        }
    }

    /**
     * Add order id conditions
     * @param $sql
     * @param $binds
     */
    public function addItemIdCondition(&$sql, &$binds) {
        if (!empty($this->id)) {
            $sql .= ' AND oi."id" = :id';
            $binds[':id'] = $this->id;
        }
    }

    /**
     * Add order id conditions
     * @param $sql
     * @param $binds
     */
    public function addOnlyActiveCondition(&$sql, &$binds) {
        if (boolval($this->onlyActive)) {
            $sql .= ' AND oi."deletedAt" IS NULL';
        }
    }

    /**
     * Add order item id conditions
     * @param $sql
     */
    public function addItemIdsCondition(&$sql) {
        if (!empty($this->ids)) {
            $sql .= ' AND oi."id" IN ('
                . implode(', ', array_map(function($id) {
                    return intval($id);
                }, $this->ids))
                . ') ';
        }
    }


    /**
     * Add all conditions to where clouse
     * @param $sql
     * @param $binds
     */
    public function addAllConditions(&$sql, &$binds) {
        $this->addOrderIdCondition($sql, $binds);
        $this->addItemIdCondition($sql, $binds);
        $this->addItemIdsCondition($sql);
        $this->addOnlyActiveCondition($sql,$binds);
    }

    /**
     * Prepare result
     * @param $rows
     * @return array
     */
    private function prepareResult($rows) {
        return array_map(function ($row) {
            $row = (array)$row;
            $row['createdAt'] = locale()->dbDtToDtStr($row['createdAt']);
            $row['deletedAt'] = locale()->dbDtToDtStr($row['deletedAt']);
            return $row;
        }, $rows);
    }

    /**
     * Search order items
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
        $sql = '
            SELECT
                oi.id,
                oi."orderId",
                oi."createdAt",
                oi."deletedAt",
                oi."productId",
                p.name as "productName",
                
                oi."unitId",
                u."fullName" as "unitFullName",
                u."shortName" as "unitShortName",
                u."shortDerName" as "unitShortDerName",
                u."factor" as "unitFactor",
                p."unitStep",
                
                oi.qty,
                oi.price,
                oi.total,
                oi."realUnits",
                oi."realPrice",
                oi."realTotal"
            FROM "orderItems" oi
                INNER JOIN products p ON p.id = oi."productId"
                INNER JOIN units u ON u.id = oi."unitId"
        ';

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);

        // Result
        return $this->prepareResult(DB::select($sql, $binds));
    }

    /**
     * Select real purchased sums of order
     * @param $params
     * @return array
     */
    public function calcSums($params) {
        $this->clear();
        $this->load($params);

        //
        $binds = [];
        $sql = '
            SELECT
                oi."orderId",
                SUM(oi.total) as "total",
                SUM(oi."realTotal") as "realTotal"
            FROM "orderItems" oi
            WHERE 1=1
        ';

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);

        $sql .= ' GROUP BY oi."orderId"';

        // Result
        $rows = DB::select($sql, $binds);
        if(!empty($rows)) {
            $rows = $rows[0];
        }
        return (array)$rows;
    }
}
