<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Models\AddressModel;
use App\Models\Auth\Client;
use App\Searchers\AddressSearcher;
use Illuminate\Support\Facades\Auth;

class AddressService {

    /**
     * Cached address delivery
     * @var array
     */
    public static $addressDeliveryMap  = [];

    /**
     * Search address for clients
     * @param $client
     * @param $addressId
     * @return null
     */
    public static function search($addressId = null, $client = null) {

        // Search params
        if(!empty($client)) {
            $params = ['clientId' => $client->id];
        }
        if(!empty($addressId)) {
            $params['id'] = $addressId;
        }
        $params['sort'] = 'createdAt';
        $params['sortDirect'] = 'desc';

        $s = new AddressSearcher();
        $addresses = $s->search($params);
        if(empty($addresses)) {
            return [];
        }
        return $addresses;
    }

    /**
     * Search only clients addresses
     * @param $client
     * @return null
     */
    public static function searchClientAddresses($client) {
        return static::search(null, $client);
    }

    /**
     * Create new address for client
     * @param Client $client
     * @param array $data
     * @return AddressModel
     */
    public static function addAddress($client, $data) {
        $a = new AddressModel();
        $a->clientId = $client->id;
        $a->city = $data['city'];
        $a->street = $data['street'];
        $a->house = $data['house'] ?? '';
        $a->building = $data['building'] ?? '';
        $a->entrance = $data['entrance'] ?? '';
        $a->floor = $data['floor'] ?? '';
        $a->doorphone = $data['doorphone'] ?? '';
        $a->appt = $data['appt'] ?? '';
        $a->lat = $data['lat'];
        $a->lon = $data['lon'];
        $a->comment = $data['comment'] ?? '';
        $a->name = $data['name'];
        $a->createdAt = DbHelper::currTs();
        $a->save();

        return $a;
    }

    /**
     * Search single address
     * @param $client
     * @param $addressId
     * @return null
     */
    public static function searchOne($addressId, $client = null) {
        $addresses = static::search($addressId, $client);
        if(!empty($addresses)) {
            return $addresses[0];
        }
        return null;
    }

    /**
     * Delete address
     * @param $client
     * @param $addressId
     */
    public static function delete($client, $addressId) {
        AddressModel::where('clientId', $client->id)
                ->where('id', $addressId)->first()->delete();
    }


    /**
     * Find delivery area with cached
     * @param $addressId
     * @return null|array
     */
    public static function getDeliveryArea($addressId) {

        if(array_key_exists($addressId, static::$addressDeliveryMap)) {
            return static::$addressDeliveryMap[$addressId];
        }

        $a = static::searchOne($addressId);
        if (empty($a['daName'] ?? '')) {
            return null;
        }
        static::$addressDeliveryMap[$addressId] = $a;
        return $a;
    }

    /**
     * Is delivery available to address?
     * @param $addressId
     * @param $productsSum
     * @return bool|null
     */
    public static function calcDelivery($addressId, $productsSum) {
        $da = static::getDeliveryArea($addressId);
        // strangely if delivery sum is empty
        if (is_null($da) or is_null($da['daPrice'] ?? null)) {
            return null;
        }
        // if products sum more than sum for free delivery
        if(intval($productsSum) >= intval($da['daFreeSum'])) {
            return 0;
        }
        return intval($da['daPrice']);
    }

    /**
     * Get delivery area id by address id
     * @param $addressId
     * @param $sum
     * @return bool
     */
    public static function getDeliveryAreaId($addressId) {
        $a = static::getDeliveryArea($addressId);
        if (empty($a['daId'] ?? 0)) {
            return null;
        }
        return intval($a['daId']);
    }

}
