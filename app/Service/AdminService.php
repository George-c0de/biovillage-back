<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Models\Auth\Admin;
use App\Models\CatalogSection;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * Admin Service. Working with admins
 *
 * @package App\Service\AdminService
 */
class AdminService
{

    const SORT_DT = 'dt';
    const SORT_NAME = 'name';
    const SORTS_FIELDS = [ self::SORT_DT, self::SORT_NAME ];

    /**
     * Search admins
     *
     * @param array $params
     *  sort
     *  sortDirect
     * @param int $targetId Unnecessary Admin ID
     * @return array
     */
    public static function list(array $params = [], $targetId = null)
    {
        $q = Admin::orderBy(
            $params['sort'] ?? self::SORT_NAME,
            $params['sortDirect'] ?? 'asc'
        );
        if(!is_null($targetId)) {
            $q->where('id', '=', $targetId);
        }
        return array_map(function($row) {
            $row['roles'] = DbHelper::pgArrayToArray($row['roles']);
            $row['createdAt'] = locale()->dbDtToDtStr($row['createdAt']);
            $row['updatedAt'] = locale()->dbDtToDtStr($row['updatedAt']);
            $row['phone'] = PhoneNumber::make($row['phone'])->formatInternational();
            return $row;
        },$q->get()->toArray());
    }

    /**
     * Get admin info
     * Admin list is small that do so
     * @param $adminId
     * @return mixed|null
     */
    public static function get($adminId) {
        $admins = static::list([], $adminId);
        if(!empty($admins)) {
            return $admins[0];
        }
        return null;
    }

    /**
     * Delete admin
     * @param $admin
     * @throws \Exception
     */
    public static function delete($admin) {
        if(!$admin instanceof Admin) {
            $admin = Admin::findOrFail($admin);
        }
        $admin->delete();
    }

    /**
     * Create new admin
     * @param $data
     * @return Admin $admin
     */
    public static function create($data) {
        $data['roles'] = DbHelper::arrayToPgArray($data['roles'] ?? []);
        $data['phone'] = PhoneService::preparePhoneNumber($data['phone']);
        $data['password'] = Hash::make($data['password']);
        $data['createdAt'] = DbHelper::currTs();
        $data['updatedAt'] = DbHelper::currTs();
        $admin = new Admin();
        $admin->fill($data);
        $admin->save();

        return static::get($admin->id);
    }

    /**
     * Edit admin data
     * @param $admin
     * @param $data
     * @return
     */
    public function edit($admin, $data) {
        if(!$admin instanceof Admin) {
            $admin = Admin::findOrFail($admin);
        }
        if(array_key_exists('roles', $data)) {
            $data['roles'] = DbHelper::arrayToPgArray($data['roles']);
        }
        $admin->fill($data);
        $admin->save();
        return $admin;
    }
}
