<?php

namespace App\Searchers;

use App\Helpers\DbHelper;
use App\Models\Auth\Client;
use App\Models\ProductModel;
use App\Service\ClientService;
use App\Service\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ClientSearcher extends BaseSearcher {

    const DEFAULT_LIMIT = 10;

    /**
     * @var $id
     */
    public $id;

    /**
     * @var $name
     */
    public $name;

    /**
     * @var $dtRegBegin
     */
    public $dtRegBegin;

    /**
     * @var $dtRegEnd
     */
    public $dtRegEnd;

    /**
     * @var $dtLastLoginBegin
     */
    public $dtLastLoginBegin;

    /**
     * @var $dtLastLoginEnd
     */
    public $dtLastLoginEnd;

    /**
     * @var $platform
     */
    public $platform;

    /**
     * @var $phone
     */
    public $phone;

    /**
     * @var $invitedById
     */
    public $invitedById;

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
    public function addClientId(&$sql, &$binds) {
        if (!empty($this->id )) {
            $sql .= ' AND c.id = :clientId';
            $binds[':clientId'] = $this->id;
        }
    }


    /**
     * Add name conditions
     * @param $sql
     * @param $binds
     */
    public function addNameConditions(&$sql, &$binds) {
        if(!empty($this->name)) {
            $sql .= ' AND c.name ILIKE :name';
            $binds['name'] = '%' . $this->name . '%';
        }
    }

    /**
     * Add phone conditions
     * @param $sql
     * @param $binds
     */
    public function addPhoneConditions(&$sql, &$binds) {
        if(!empty($this->phone)) {
            $sql .= ' AND c.phone LIKE :phone';
            $binds['phone'] = '%' . $this->phone . '%';
        }
    }

    /**
     * Condition by reg date
     * @param $sql
     * @param $binds
     */
    public function addRegConditions(&$sql, &$binds) {
        if(!empty($this->dtRegBegin)) {
            $sql .= ' AND c."createdAt" >= :dtRegBegin';
            $binds[':dtRegBegin'] =
                locale()->dateToDbStr($this->dtRegBegin);

        }
        if(!empty($this->dtRegEnd)) {
            $sql .= ' AND c."createdAt" <= :dtRegEnd';
            $binds[':dtRegEnd'] =
                locale()->dateToDbStr($this->dtRegEnd);
        }
    }

    /**
     * Condition by last login date
     * @param $sql
     * @param $binds
     */
    public function addLastLoginConditions(&$sql, &$binds) {
        if(!empty($this->dtLastLoginBegin)) {
            $sql .= ' AND c."lastLoginAt" >= :dtLastLoginBegin';
            $binds[':dtLastLoginBegin'] =
                locale()->dateToDbStr($this->dtLastLoginBegin);
        }
        if(!empty($this->dtLastLoginEnd)) {
            $sql .= ' AND c."lastLoginAt" <= :dtLastLoginEnd';
            $binds[':dtLastLoginEnd'] =
                locale()->dateToDbStr($this->dtLastLoginEnd);
        }
    }

    /**
     * Add platform conditions
     * @param $sql
     * @param $binds
     */
    public function addPlatformConditions(&$sql, &$binds) {
        if(!empty($this->platform)) {
            $sql .= ' AND c."lastPlatform" = :platform';
            $binds[':platform'] = $this->platform;
        }
    }

    /**
     * Add platform conditions
     * @param $sql
     * @param $binds
     */
    public function addInvitedByConditions(&$sql, &$binds) {
        if(!empty($this->invitedById)) {
            $sql .= ' AND c."invitedBy" = :invitedById';
            $binds[':invitedById'] = $this->invitedById;
        }
    }


    /**
     * Add sort
     * @param $sql
     */
    public function addSort(&$sql) {
        if(empty($this->sort)) {
            $this->sort = Client::SORT_REG;
        }
        if(empty($this->sortDirect)) {
            $this->sortDirect =  'ASC';
        }

        $sortSqlFmt = [
            Client::SORT_REG => 'c."createdAt" %s, c.name',
            Client::SORT_NAME => 'c.name %s, c.id',
            Client::SORT_LAST_LOGIN => 'c."lastLoginAt" %s, c.name',
        ][$this->sort] ?? 'c."createdAt" %s';
        $sortSqlFmt = ' ORDER BY ' . $sortSqlFmt;

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
        $this->addNameConditions($sql, $binds);
        $this->addClientId($sql, $binds);
        $this->addLastLoginConditions($sql, $binds);
        $this->addRegConditions($sql, $binds);
        $this->addPlatformConditions($sql, $binds);
        $this->addPhoneConditions($sql, $binds);
        $this->addInvitedByConditions($sql, $binds);
    }


    /**
     * Prepare result
     * @param $rows
     * @return array
     */
    private function prepareResult($rows) {
        return array_map(function($row) {
            $row = (array)$row;
            $row['createdAt'] = locale()->dbDtToDtStr($row['createdAt']);
            $row['updatedAt'] = locale()->dbDtToDtStr($row['updatedAt']);
            $row['lastLoginAt'] = locale()->dbDtToDtStr($row['lastLoginAt']);
            $row['birthday'] = locale()->dbDateToDateStr($row['birthday']);
            $row['phone'] = Phone::toShow($row['phone']);
            return $row;
        }, $rows);
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
        $sql = '
            SELECT
                c.*,
                i.name as "invitedByName",
                i.phone as "invitedByPhone"
            FROM clients c
                LEFT JOIN clients i ON i.id = c."invitedBy"
            WHERE 1=1
        ';

        // Conditions, sort and limit
        $this->addAllConditions($sql, $binds);

        $this->addSort($sql);
        $this->addLimitOffset($sql, $binds);

        return $this->prepareResult(DB::select($sql, $binds));
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
            SELECT COUNT(c.id) as cnt
            FROM clients c
            WHERE 1=1
        ';
        $this->addAllConditions($sql,$binds );
        $res = DB::select($sql, $binds);
        if(!empty($res)) {
            return (int)$res[0]->cnt;
        }
        return 0;
    }

    /**
     * Only one client
     * @param $id
     * @return null
     */
    public function searchOne($id) {
        $clients = $this->search(['id' => $id]);
        if(empty($clients)) {
            return null;
        }
        return $clients[0];
    }

}