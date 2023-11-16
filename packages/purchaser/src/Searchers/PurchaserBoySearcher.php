<?php

namespace Packages\Purchaser\Searchers;

use App\Searchers\BaseSearcher;
use Illuminate\Support\Facades\DB;

class PurchaserBoySearcher extends BaseSearcher {

    /**
     * @var $date - Delivery date
     */
    public $date;

    /**
     * @var $startHourBegin
     */
    public $startHour;

    /**
     * @var $startHourEnd
     */
    public $endHour;

    /**
     * @var $orderStatus
     */
    public $orderStatus;

    /**
     * Filter by delivery date
     * @param $sql
     * @param $binds
     */
    public function addDeliveryDateCondition(&$sql, &$binds) {
        if (!empty($this->date )) {
            $sql .= ' AND o."deliveryDate" = :deliveryDate';
            $binds[':deliveryDate'] = locale()->dateToDbStr($this->date);
        }
    }

    /**
     * Filter by delivery date
     * @param $sql
     * @param $binds
     */
    public function addStartAndEndHourCondition(&$sql, &$binds) {
        if (!empty($this->startHour)) {
            $sql .= ' AND di."startHour" >= :startHour';
            $binds[':startHour'] = $this->startHour;
        }
        if (!empty($this->endHour)) {
            $sql .= ' AND di."startHour" <= :endHour';
            $binds[':endHour'] = $this->endHour;
        }
    }

    /**
     * Filter by order status
     * @param $sql
     * @param $binds
     */
    public function addOrderStatusConditions(&$sql, &$binds) {
        if (!empty($this->orderStatus)) {
            $sql .= ' AND o."status" = :orderStatus';
            $binds[':orderStatus'] = $this->orderStatus;
        }
    }


    /**
     * Add all conditions to where clouse
     * @param $sql
     * @param $binds
     */
    public function addAllConditions(&$sql, &$binds) {
        $this->addDeliveryDateCondition($sql, $binds);
        $this->addStartAndEndHourCondition($sql, $binds);
        $this->addOrderStatusConditions($sql, $binds);
    }

    /**
     * Prepare result
     * @param $rows
     * @return array
     */
    private function prepareResult($rows) {
        return array_map(function ($row) {
            $row = (array)$row;
            return $row;
        }, $rows);

    }


    /**
     * Search products for purchases
     * @param array $params
     * @param $getSqlParams
     * @return mixed
     * @throws \ReflectionException
     */
    public function searchProducts(array $params = [], $getSqlParams = false)
    {
        $this->clear();
        $this->load($params);
        //
        $binds = [];
        $sql = '
            SELECT
                p.id,
                p.name as "productName",
                g.name as "groupName",
                g.id as "groupId",
                SUM(oi.qty) as "totalQty",
                SUM(oi.qty * p."unitStep" ) as "totalUnits",
                p."netCostPerStep",
                SUM(oi.qty * p."netCostPerStep") as "total",
                u."fullName" as "unitFullName",
                u."shortName" as "unitShortName",
                u."shortDerName" as "unitShortDerName",
                u."factor" as "unitFactor",
                p."unitStep" as "unitStep",
                SUM(COALESCE(oi."realUnits", 0)) as "realUnits",
                SUM(COALESCE(oi."realTotal", 0)) as "realTotal",
                oi."realPrice"
            FROM orders o
                INNER JOIN "orderItems" oi ON oi."orderId" = o.id
                INNER JOIN products p ON p.id = oi."productId"
                INNER JOIN "deliveryIntervals" di ON di.id = o."deliveryIntervalId"
                INNER JOIN units u ON u.id = oi."unitId"
                INNER JOIN groups g ON g.id = p."groupId"
            WHERE 1=1
        ';

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);

        $sql .= ' GROUP BY o."deliveryDate", p.id, oi.price, oi."realPrice", u.id,  g.id';
        $sql .= ' ORDER BY "productName"';

        if($getSqlParams) {
            return [
                $sql,
                $binds,
            ];
        }

        // Result
        return $this->prepareResult(DB::select($sql, $binds));
    }

    /**
     * Search total gifts set
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    public function searchGifts(array $params = [])
    {
        $this->clear();
        $this->load($params);

        //
        $binds = [];
        $sql = '
            SELECT
                g.id,
                g.name as "giftName",
                SUM(og.qty) as "totalQty",
                og.bonuses,
                SUM(og."totalBonuses") as "totalBonuses"
            FROM orders o
                INNER JOIN "orderGifts" og ON og."orderId" = o.id
                INNER JOIN gifts g ON g.id = og."giftId"
                INNER JOIN "deliveryIntervals" di ON di.id = o."deliveryIntervalId"
            WHERE 1=1  
        ';

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);

        $sql .= ' GROUP BY o."deliveryDate", g.id, og.bonuses';
        $sql .= ' ORDER BY "giftName"';

        // Result
        return $this->prepareResult(DB::select($sql, $binds));
    }

}