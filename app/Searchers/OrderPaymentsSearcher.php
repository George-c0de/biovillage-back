<?php

namespace App\Searchers;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\AddressModel;
use App\Models\Auth\Client;
use App\Models\GiftModel;
use App\Models\GroupModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Service\ClientService;
use App\Service\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OrderPaymentsSearcher extends BaseSearcher {

    const DEFAULT_LIMIT = 10;

    /**
     * @var $orderId
     */
    public $orderId;

    /**
     * @var $clientId
     */
    public $clientId;

    /**
     * Type of payment
     * @var $type
     */
    public $type;

    /**
     * Sorting direct
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
     * Show or not confirmation details
     * @var bool $showConfirmation
     */
    public $showConfirmation = false;

    /**
     * Add order id conditions
     * @param $sql
     * @param $binds
     */
    public function addOrderIdCondition(&$sql, &$binds) {
        if (!empty($this->orderId)) {
            $sql .= ' AND p."orderId" = :orderId';
            $binds[':orderId'] = $this->orderId;
        }
    }

    /**
     * Add client id conditions
     * @param $sql
     * @param $binds
     */
    public function addClientIdCondition(&$sql, &$binds) {
        if (!empty($this->clientId)) {
            $sql .= ' AND o."clientId" = :clientId';
            $binds[':clientId'] = $this->clientId;
        }
    }

    /**
     * Add type condition
     * @param $sql
     * @param $binds
     */
    public function addTypeCondition(&$sql, &$binds) {
        if (!empty($this->type)) {
            $sql .= ' AND p."type" = :type';
            $binds[':type'] = $this->type;
        }
    }

    /**
     * Add all conditions to where clouse
     * @param $sql
     * @param $binds
     */
    public function addAllConditions(&$sql, &$binds) {
        $this->addOrderIdCondition($sql, $binds);
        $this->addClientIdCondition($sql, $binds);
        $this->addTypeCondition($sql, $binds);
    }

    /**
     * Sort settings
     * @param $sql
     * @param $binds
     */
    public function addSort(&$sql, &$binds) {
        if(empty($this->sortDirect)) {
            $this->sortDirect = 'ASC';
        }
        $sql .= ' ORDER BY o."createdAt" ' . $this->sortDirect;
    }

    /**
     * Add limit for sql request
     * @param $sql
     * @param $binds
     */
    public function addLimitAndOffset(&$sql, &$binds) {
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
     * Prepare result
     * @param $rows
     * @return array
     */
    private function prepareResult($rows) {
        return array_map(function ($row) {
            $row = (array)$row;
            foreach(['createdAt', 'madeAt', 'canceledAt', 'orderCreatedAt'] as $df) {
                $row[$df] = locale()->dbDtToDtStr($row[$df]);
            }
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
                p.id,
                p."createdAt",
                p."madeAt",
                p."canceledAt",
                p.transaction,
                p.method,
                p.total,
                p.data,
                p.status,
                p."orderId",
                o."createdAt" as "orderCreatedAt",
                o.status as "orderStatus"
            ' . ( $this->showConfirmation ? ', p.confirmation' : '' ) . ' 
            FROM payments p
                INNER JOIN orders o ON o.id = p."orderId"
            WHERE 1=1
        ';

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);
        $this->addSort($sql, $binds);
        $this->addLimitAndOffset($sql, $binds);

        // Result
        return $this->prepareResult(DB::select($sql, $binds));
    }

    /**
     * Search only primary payment
     * @param $orderId
     * @return mixed
     */
    public function getPrimaryPayment($orderId) {
        $payments = $this->search([
            'orderId' => $orderId,
            'limit' => 1,
            'sortDirect' => 'ASC'
        ]);
        if(empty($payments)) {
            Utils::raise('No primary payments found');
        }
        return $payments[0];
    }

    /**
     * Sum all payments
     * @param $params
     * @return array
     */
    public function sumPayments($params) {
        $this->clear();
        $this->load($params);

        $binds = [];
        $sql = '
            SELECT
                p."orderId",
                SUM(p.total) as total
            FROM payments p
                 INNER JOIN orders o ON o.id = p."orderId"
            WHERE 1=1
        ';
        $this->addAllConditions($sql, $binds);

        $sql .= ' GROUP BY p."orderId"';

        return Db::select($sql, $binds);
    }

    /**
     * Sum payments for only one order
     * @param $orderId
     * @return null
     */
    public function calcOrderPayments($orderId) {
        $sums = $this->sumPayments(['orderId' => $orderId]);
        if(empty($sums)) {
            return null;
        }

        return $sums[0]->total;
    }
}