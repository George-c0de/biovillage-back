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
use App\Service\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class BonusesSearcher extends BaseSearcher {

    /**
     * @var $id
     */
    public $clientId;

    /**
     * @var $orderStatuses
     */
    public $orderStatuses;

    /**
     * @var $orderId
     */
    public $orderId;

    /**
     * @var $onlyActive
     */
    public $onlyActive;

    /**
     * Add client id param
     * @param $sql
     * @param $binds
     */
    public function addClientIdConditions(&$sql, &$binds) {
        if(!empty($this->clientId)) {
            $sql .= ' AND o."clientId" = :clientId';
            $binds[':clientId'] = $this->clientId;
        }
    }

    /**
     * Add only active gifts
     * @param $sql
     * @param $binds
     */
    public function addOnlyActiveConditions(&$sql, &$binds) {
        if(boolval($this->onlyActive)) {
            $sql .= ' AND og."deletedAt" IS NULL';
        }
    }

    /**
     * Order statuses
     * @param $sql
     * @param $binds
     */
    public function addOrderStatusesConditions(&$sql, &$binds) {

        if(!empty($this->orderStatuses)) {
            $statuses = Utils::safeArray($this->orderStatuses);
            $sql .= ''
                . ' AND o.status IN ('
                . implode(', ', array_map(function($status) use (&$binds) {
                    $k = ':status' . $status;
                    $binds[$k] = $status;
                    return $k;
                }, $statuses))
                . ')';
        }
    }

    /**
     * Order id conditions
     * @param $sql
     * @param $binds
     */
    public function addOrderIdConditions(&$sql, &$binds) {
        if(!empty($this->orderId)) {
            $sql .= ' AND o.id = :orderId';
            $binds[':orderId'] = $this->orderId;
        }
    }

    /**
     * Search more detailed results
     * @param $params
     * @return mixed
     */
    public function calcLockedBonuses($params) {
        $this->clear();

        $params['onlyActive'] = true;
        $this->load($params);

        //
        $binds = [];
        $sql = '            
            SELECT
                o."clientId",
                c.bonuses,
                sum(og.bonuses) as "lockedBonuses",
                sum(og."totalBonuses") as "writeOffBonuses"
            FROM orders o
                INNER JOIN "orderGifts" as og ON og."orderId" = o.id
                INNER JOIN clients c ON c.id = o."clientId"
            WHERE 1=1
        ';

        $this->addClientIdConditions($sql, $binds);
        $this->addOrderStatusesConditions($sql, $binds);
        $this->addOrderIdConditions($sql, $binds);
        $this->addOnlyActiveConditions($sql, $binds);

        $sql .= '
            GROUP BY c.id, o."clientId"
        ';

        // Result
        return (array)DB::selectOne($sql, $binds);
    }

}