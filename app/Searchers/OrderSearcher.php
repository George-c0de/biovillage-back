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

class OrderSearcher extends BaseSearcher {

    const DEFAULT_LIMIT = 10;

    /**
     * @var $id
     */
    public $id;

    /**
     * Filter by created date
     * @var $dtCreatedBegin;
     */
    public $dtCreatedBegin;

    /**
     * Filter by created date
     * @var $dtCreatedEnd;
     */
    public $dtCreatedEnd;

    /**
     * Filter by single created date
     * @var $dtCreatedEnd;
     */
    public $dtCreated;

    /**
     * Filter by finished date
     * @var $dtFinishedBegin;
     */
    public $dtFinishedBegin;

    /**
     * Filter by finished date
     * @var $dtFinishedEnd;
     */
    public $dtFinishedEnd;

    /**
     * Filter by single finished date
     * @var $dtFinishedEnd;
     */
    public $dtFinished;


    /**
     * Filter by delivery date
     * @var $dtFinishedBegin;
     */
    public $dtDeliveryBegin;

    /**
     * Filter by delivery date end
     * @var $dtFinishedEnd;
     */
    public $dtDeliveryEnd;

    /**
     * Filter by single delivery
     * @var $dtFinishedEnd;
     */
    public $dtDelivery;


    /**
     * Filter by client id
     * @var $clientId
     */
    public $clientId;

    /**
     * Filter by status
     * @var $status
     */
    public $status;

    /**
     * @var $dtPackedBegin - Start date of packed
     */
    public $dtPackedBegin;

    /**
     * @var $dtPackedEnd - End date of packed
     */
    public $dtPackedEnd;

    /**
     * @var $dtPackedEnd - Date of packed
     */
    public $dtPacked;

    /**
     * @var $deliveryHourBegin - Start hour of delivery
     */
    public $deliveryHourBegin;

    /**
     * @var $deliveryHourEnd - End hour of delivery
     */
    public $deliveryHourEnd;

    /**
     * @var $clientPhone
     */
    public $clientPhone;


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
     * Option for show confirm url in case bank card payment
     * @var $showPaymentConfirmation
     */
    public $showPaymentConfirmation = false;

    /**
     * Add id conditions
     * @param $sql
     * @param $binds
     */
    public function addIdCondition(&$sql, &$binds) {
        if (!empty($this->id )) {
            $sql .= ' AND o.id = :id';
            $binds[':id'] = $this->id;
        }
    }

    /**
     * Add status conditions
     * @param $sql
     * @param $binds
     */
    public function addStatusCondition(&$sql, &$binds) {
        if (!empty($this->status)) {
            $sql .= ' AND o.status = :status';
            $binds[':status'] = $this->status;
        }
    }

    /**
     * Add client id
     * @param $sql
     * @param $binds
     */
    public function addClientIdCondition(&$sql, &$binds) {
        if (!empty($this->clientId )) {
            $sql .= ' AND o."clientId" = :clientId';
            $binds[':clientId'] = $this->clientId;
        }
    }


    /**
     * Add client id
     * @param $sql
     * @param $binds
     */
    public function addClientPhoneCondition(&$sql, &$binds) {
        if (!empty($this->clientPhone )) {
            $sql .= ' AND c."phone" LIKE :clientPhone';
            $binds[':clientPhone'] = '%' . $this->clientPhone . '%';
        }
    }

    /**
     * Condition by created
     * @param $sql
     * @param $binds
     */
    public function addCreatedAtCondition(&$sql, &$binds) {
        if(!empty($this->dtCreatedBegin)) {
            $sql .= ' AND o."createdAt" >= :dtCreatedBegin';
            $binds[':dtCreatedBegin'] =
                locale()->dateToDbStr($this->dtCreatedBegin);
        }
        if(!empty($this->dtCreatedEnd)) {
            $sql .= ' AND o."createdAt" <= DATE_TRUNC(
                \'day\', :dtCreatedEnd::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtCreatedEnd'] =
                locale()->dateToDbStr($this->dtCreatedEnd);
        }
        if(!empty($this->dtCreated)) {
            $sql .= ' AND o."createdAt" >= :dtCreated::date';
            $sql .= ' AND o."createdAt" <= DATE_TRUNC(
                \'day\', :dtCreated::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtCreated'] = locale()->dateToDbStr($this->dtCreated);
        }
    }

    /**
     * Condition by created
     * @param $sql
     * @param $binds
     */
    public function addFinishedCondition(&$sql, &$binds) {
        if(!empty($this->dtFinishedBegin)) {
            $sql .= ' AND o."finishedAt" >= :dtFinishedBegin';
            $binds[':dtFinishedBegin'] =
                locale()->dateToDbStr($this->dtFinishedBegin);

        }
        if(!empty($this->dtFinishedEnd)) {
            $sql .= ' AND o."finishedAt" <= DATE_TRUNC(
                \'day\', :dtFinishedEnd::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtFinishedEnd'] =
                locale()->dateToDbStr($this->dtFinishedEnd);

        }
        if(!empty($this->dtFinished)) {
            $sql .= ' AND o."finishedAt" >= :dtFinished::date';
            $sql .= ' AND o."finishedAt" <= DATE_TRUNC(
                \'day\', :dtFinished::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtFinished'] = locale()->dateToDbStr($this->dtFinished);
        }
    }

    /**
     * Condition by delivery date
     * @param $sql
     * @param $binds
     */
    public function addDeliveryDateCondition(&$sql, &$binds) {
        if(!empty($this->dtDeliveryBegin)) {
            $sql .= ' AND o."deliveryDate" >= :dtDeliveryBegin';
            $binds[':dtDeliveryBegin'] =
                locale()->dateToDbStr($this->dtDeliveryBegin);
        }
        if(!empty($this->dtDeliveryEnd)) {
            $sql .= ' AND o."deliveryDate" <= DATE_TRUNC(
                \'day\', :dtDeliveryEnd::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtDeliveryEnd'] =
                locale()->dateToDbStr($this->dtDeliveryEnd);
        }
        if(!empty($this->dtDelivery)) {
            $sql .= ' AND o."deliveryDate" >= :dtDelivery::date';
            $sql .= ' AND o."deliveryDate" <= DATE_TRUNC(
                \'day\', :dtDelivery::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtDelivery'] = locale()->dateToDbStr($this->dtDelivery);
        }
    }

    /**
     * Condition by delivery date
     * @param $sql
     * @param $binds
     */
    public function addPackedDateCondition(&$sql, &$binds) {
        if(!empty($this->dtPackedBegin)) {
            $sql .= ' AND o."packedAt" >= :dtPackedBegin';
            $binds[':dtPackedBegin'] =
                locale()->dateToDbStr($this->dtPackedBegin);
        }
        if(!empty($this->dtPackedEnd)) {
            $sql .= ' AND o."packedAt" <= DATE_TRUNC(
                \'day\', :dtPackedEnd::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtPackedEnd'] =
                locale()->dateToDbStr($this->dtPackedEnd);
        }
        if(!empty($this->dtPacked)) {
            $sql .= ' AND o."packedAt" >= :dtPacked::date';
            $sql .= ' AND o."packedAt" <= DATE_TRUNC(
                \'day\', :dtPacked::date) + (24*60*60 - 1) * interval \'1 second\'';
            $binds[':dtPacked'] = locale()->dateToDbStr($this->dtPacked);
        }
    }

    /**
     * Delivery hour conditions
     * @param $sql
     * @param $binds
     */
    public function addDeliveryHour(&$sql, &$binds) {
        if(!empty($this->deliveryHourBegin)) {
            $sql .= ' AND di."startHour" >= :startHour';
            $binds[':startHour'] = $this->deliveryHourBegin;
        }
        if(!empty($this->deliveryHourEnd)) {
            $sql .= ' AND di."endHour" <= :endHour';
            $binds[':endHour'] = $this->deliveryHourEnd;
        }
    }

    /**
     * Add sort
     * @param $sql
     */
    public function addSort(&$sql) {
        if(empty($this->sort)) {
            $this->sort = OrderModel::SORT_CREATED;
            $this->sortDirect =  'DESC';
        }
        if(empty($this->sortDirect)) {
            $this->sortDirect =  'ASC';
        }
        $sortSqlFmt = [
            OrderModel::SORT_CREATED => 'o."createdAt" %s',
            OrderModel::SORT_CLIENT => '"clientName" %s',
            OrderModel::SORT_UPDATED => 'o."updatedAt" %s',
            OrderModel::SORT_PACKED => 'o."packedAt" %s',
            OrderModel::SORT_FINISHED => 'o."finishedAt" %s, o.id',
            OrderModel::SORT_STATUS => 'o."status" %s',
            OrderModel::SORT_INTERVAL => 'di."dayOfWeek", di."startHour" %s',
            OrderModel::SORT_DELIVERY => 'o."deliveryDate" %s, di."startHour"'
        ][$this->sort] ?? 'o."createdAt" %s';

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
        $this->addClientIdCondition($sql, $binds);
        $this->addCreatedAtCondition($sql, $binds);
        $this->addDeliveryDateCondition($sql, $binds);
        $this->addFinishedCondition($sql, $binds);
        $this->addStatusCondition($sql, $binds);
        $this->addPackedDateCondition($sql, $binds);
        $this->addDeliveryHour($sql, $binds);
        $this->addClientPhoneCondition($sql, $binds);
    }

    /**
     * Prepare result
     * @param $rows
     * @param bool $detailed
     * @return array
     */
    private function prepareResult($rows, $detailed = false) {
        $prepareFunc = function($row) {
            $row = (array)$row;
            $row['deliveryDate'] = locale()->dbDateToDateStr($row['deliveryDate']);
            $row['createdAt'] = locale()->dbDtToDtStr($row['createdAt']);
            $row['updatedAt'] = locale()->dbDtToDtStr($row['updatedAt']);
            $row['finishedAt'] = locale()->dbDtToDtStr($row['finishedAt']);
            $row['packedAt'] = locale()->dbDtToDtStr($row['packedAt']);
            $row['addressLat'] = floatval($row['addressLat']);
            $row['addressLon'] = floatval($row['addressLon']);
            $row['clientPhone'] = Phone::toShow($row['clientPhone']);
            return $row;
        };

        $prepareDetailed = function ($row) use($prepareFunc) {
            $row = $prepareFunc($row);

            // Payments - OK
            $row['paymentData'] = json_decode($row['paymentData'], true) ?? [];
            $row['paymentData'] = array_map(function($payment) {
                $payment['createdAt'] = locale()->dbDtToDtStr($payment['createdAt']);
                $payment['madeAt'] = locale()->dbDtToDtStr($payment['madeAt']);
                $payment['refundedAt'] = locale()->dbDtToDtStr($payment['refundedAt']);
                $payment['canceledAt'] = locale()->dbDtToDtStr($payment['canceledAt']);
                return $payment;
            }, $row['paymentData']);

            // Payment. Need set full url for images
            $row['giftData'] = json_decode($row['giftData'], true) ?? [];
            if(!empty($row['giftData'])) {
                $row['giftData'] = array_map(function ($gift) {
                    $gift['images'] = array_map(function ($giftIm) {
                        $giftIm[1] = Utils::fullUrl($giftIm[1]);
                        $giftIm[2] = Utils::fullUrl($giftIm[2]);
                        return $giftIm;
                    }, $gift['images'] ?? []);
                    return $gift;
                }, $row['giftData']);
            }

            // Items. Need set full url
            $row['itemsData'] = json_decode($row['itemsData'], true) ??
                Utils::raise('No items');
            $row['itemsData'] = array_map(function($item) {
                $item['images'] = array_map(function($prodIm) {
                    $prodIm[1] = Utils::fullUrl($prodIm[1]);
                    $prodIm[2] = Utils::fullUrl($prodIm[2]);
                    return $prodIm;
                }, $item['images'] ?? []);
                return $item;
            }, $row['itemsData']);

            //
            return $row;
        };

        return array_map( $detailed ? $prepareDetailed : $prepareFunc, $rows);
    }

    /**
     * Get main sql
     */
    private function coreSql() {
        return '
            SELECT
                o.id,
                o."createdAt",
                o."updatedAt",
                o."finishedAt",
                o."packedAt",
                o."placedAt",
                o.status,
                o."clientsComment",
                o."adminsComment",
                o."commentForClient",
                o."actionIfNotDelivery",
                o."promoCode",
                o.platform,
                o."clientId",
                c.name as "clientName",
                c.phone as "clientPhone",
                o."addressId",
                a.name as "addressName",
                a.city as "addressCity",
                a.street as "addressStreet",
                a.house as "addressHouse",
                a.building as "addressBuilding",
                a.entrance as "addressEntrance",
                a.floor as "addressFloor",
                a.doorphone as "addressDoorphone",
                a.appt as "addressAppt",
                a.lat as "addressLat",
                a.lon as "addressLon",
                a.comment as "addressComment",
                o."deliveryIntervalId",
                di."dayOfWeek" as "diDayOfWeek",
                di."startHour" as "diStartHour",
                di."startMinute" as "diStartMinute",
                di."endHour" as "diEndHour",
                di."endMinute" as "diEndMinute",
                o."deliveryAreaId",
                da.name as "daName",
                o."deliveryDate",
                o."productsSum",
                o."deliverySum",
                o.bonuses,
                o.total,
                o.error
            FROM orders o
                INNER JOIN clients c ON c.id = o."clientId"
                INNER JOIN "deliveryIntervals" di ON di.id = o."deliveryIntervalId"
                INNER JOIN addresses a on a.id = o."addressId"
                INNER JOIN "deliveryArea" as da ON da.id = o."deliveryAreaId"
            WHERE 1=1
        ';
    }

    /**
     * Search orders
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
        $sql = $this->coreSql();

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);
        $this->addSort($sql);
        $this->addLimitOffset($sql, $binds);

        // Result
        return $this->prepareResult(DB::select($sql, $binds));
    }

    /**
     * Search more detailed results
     * @param $params
     * @return array
     */
    public function searchDetailed($params) {
        $this->clear();
        $this->load($params);

        //
        $binds = [];
        $coreSql = $this->coreSql();
        // Conditions, sort and limit
        $this->addAllConditions($coreSql, $binds);
        $this->addSort($sql);
        $this->addLimitOffset($coreSql, $binds);

        $sql = sprintf('
            WITH
                "targetOrders" AS ( %s ),
                "targetPayments" AS (
                    SELECT
                        p."orderId",
                        json_agg(json_build_object(
                            \'createdAt\', p."createdAt",
                            \'madeAt\', p."madeAt",
                            \'refundedAt\', p."refundedAt",
                            \'canceledAt\', p."canceledAt",
                            \'transaction\', p.transaction,
                            \'method\', p.method,
                            \'status\', p.status,
                            \'total\', p.total
                        ' . (
                                $this->showPaymentConfirmation
                                ? ', \'confirmation\', p.confirmation '
                                : ''
                            ) . '        
                        ) ORDER BY p.id) as data
                    FROM payments p
                        INNER JOIN "targetOrders" o ON o.id = p."orderId"
                    GROUP BY p."orderId"
                 ),
                "giftImages" AS (
                    SELECT
                        og."giftId",
                        (ARRAY_AGG(
                            ARRAY[
                                i.id::text,
                                i.src::text,
                                COALESCE( i."srcThumb"::text, \'\'),
                                i."order"::text
                            ]
                            ORDER BY i."order"
                        )) as images
                    FROM "targetOrders" o
                        INNER JOIN "orderGifts" og ON og."orderId" = o.id
                        INNER JOIN images i ON i."entityId" = og."giftId"
                            AND i."groupName" = :giftImageGroup
                    GROUP BY og."giftId"
                ),
                "targetGifts" AS (
                    SELECT
                        og."orderId",
                        json_agg(json_build_object(
                            \'id\', og.id,
                            \'giftId\', g.id,
                            \'name\', g.name,
                            \'price\', og.bonuses,
                            \'qty\', og.qty,
                            \'total\', og."totalBonuses",
                            \'images\', gi.images
                      ) ORDER BY g.id) as data
                    FROM "targetOrders" o
                        INNER JOIN "orderGifts" og ON og."orderId" = o.id
                        INNER JOIN gifts g ON g.id = og."giftId"
                        LEFT JOIN "giftImages" gi ON gi."giftId" = og."giftId"
                    WHERE og."deletedAt" IS NULL
                    GROUP BY og."orderId"
                ),
                "prodImages" AS (
                    SELECT
                        oi."productId",
                        (ARRAY_AGG(
                            ARRAY[
                                i.id::text,
                                i.src::text,
                                COALESCE( i."srcThumb"::text, \'\'),
                                i."order"::text
                            ]
                            ORDER BY i."order"
                         )) as images
                    FROM "targetOrders" o
                        INNER JOIN "orderItems" oi ON oi."orderId" = o.id
                        INNER JOIN images i ON i."entityId" = oi."productId" 
                            AND i."groupName" = :prodImageGroup
                    GROUP BY oi."productId"
                ),
                "targetItems" AS (
                    SELECT
                        oi."orderId",
                        json_agg(json_build_object(
                            \'id\', oi.id,
                            \'prodId\', p.id,                            
                            \'name\', p.name,
                            \'price\', oi.price,
                            \'qty\', oi.qty,
                            \'total\', oi.total,
                            \'realPrice\', oi."realPrice",
                            \'realUnits\', oi."realUnits",
                            \'realTotal\', oi."realTotal",
                            \'unitId\', oi."unitId",
                            \'unitStep\', p."unitStep",
                            \'unitMin\', p."unitStep",
                            \'unitName\', u."fullName",
                            \'unitShortName\', u."shortName",
                            \'images\', pi.images
                        ) ORDER BY oi.id) as data
                    FROM "targetOrders" o
                        INNER JOIN "orderItems" oi ON oi."orderId" = o.id
                        INNER JOIN products p ON p.id = oi."productId"
                        INNER JOIN units u ON u.id = oi."unitId"
                        LEFT JOIN "prodImages" pi ON pi."productId" = oi."productId"
                    WHERE oi."deletedAt" IS NULL
                    GROUP BY oi."orderId"
                )
            SELECT
                o.*,
                p.data as "paymentData",
                g.data as "giftData",
                i.data as "itemsData"
            FROM "targetOrders" o
                LEFT JOIN "targetPayments" p ON p."orderId" = o.id
                LEFT JOIN "targetGifts" g ON g."orderId" = o.id
                LEFT JOIN "targetItems" i ON i."orderId" = o.id
        ', $coreSql);
        $binds[':prodImageGroup'] = ProductModel::IMAGES_GROUP_NAME;
        $binds[':giftImageGroup'] = GiftModel::IMAGES_GROUP_NAME;

        $this->addSort($sql);

        // Result
        return $this->prepareResult(DB::select($sql, $binds), true);
    }

    /**
     * Calc orders count
     * @param array $params
     * @return int
     */
    public function getCount($params = []) {
        $this->clear();
        $this->load($params);
        $binds = [];
        $sql = '
            SELECT COUNT(o.id) as cnt
            FROM orders o
                INNER JOIN "deliveryIntervals" di ON di.id = o."deliveryIntervalId"
                INNER JOIN clients c ON c.id = o."clientId"
            WHERE 1=1
        ';
        $this->addAllConditions($sql, $binds);
        $res = DB::select($sql, $binds);
        if(!empty($res)) {
            return (int)$res[0]->cnt;
        }
        return 0;
    }

}
