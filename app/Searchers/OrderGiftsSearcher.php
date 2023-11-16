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

class OrderGiftsSearcher extends BaseSearcher {

    /**
     * @var $orderId
     */
    public $orderId;

    /**
     * @var $id - Order gift id
     */
    public $id;

    /**
     * @var $ids - Order gifts ids
     */
    public $ids;


    /**
     * Add order id conditions
     * @param $sql
     * @param $binds
     */
    public function addOrderIdCondition(&$sql, &$binds) {
        if (!empty($this->orderId )) {
            $sql .= ' AND og."orderId" = :orderId';
            $binds[':orderId'] = $this->orderId;
        }
    }

    /**
     * Add order id conditions
     * @param $sql
     * @param $binds
     */
    public function addGiftIdCondition(&$sql, &$binds) {
        if (!empty($this->id)) {
            $sql .= ' AND og."id" = :giftId';
            $binds[':giftId'] = $this->id;
        }
    }

    /**
     * Add order id conditions
     * @param $sql
     */
    public function addGiftIdsCondition(&$sql) {
        if (!empty($this->ids)) {
            $sql .= ' AND og."id" IN ('
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
        $this->addGiftIdCondition($sql, $binds);
        $this->addGiftIdsCondition($sql, $binds);
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
                og.id,
                og."orderId",
                og."createdAt",
                og.qty,
                og.bonuses,
                og."totalBonuses",
                og."realQty",
                og."realTotalBonuses",
                g.id as "giftId",
                g.name as "giftName"
            FROM "orderGifts" og
                INNER JOIN gifts g ON g.id = og."giftId"
            WHERE 1=1
        ';

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);

        // Result
        return $this->prepareResult(DB::select($sql, $binds));
    }
}