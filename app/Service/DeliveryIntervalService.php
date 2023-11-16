<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Models\CatalogSection;
use App\Models\DeliveryIntervalModel;
use App\Models\TagModel;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

/**
 *
 * Work with delivery intervals
 *
 * @package App\Service\DeliveryIntervalService
 */
class DeliveryIntervalService extends BaseService
{

    /**
     * Create new delivery interval
     * @param $data
     *  dayOfWeek
     *  startHour
     *  startMinute
     *  endHour
     *  endMinute
     * @return array
     */
    public static function addDeliveryInterval($data)
    {
        $di = new DeliveryIntervalModel();
        $di->fill($data);
        $di->createdAt = DbHelper::currTs();
        $di->updatedAt = DbHelper::currTs();
        $di->save();

        return $di;
    }

    /**
     * Edit delivery interval
     * @param $interval
     * @param $data
     * @return array
     */
    public static function editDeliveryInterval($interval, $data) {

        if(!$interval instanceof DeliveryIntervalModel) {
            $interval = DeliveryIntervalModel::findOrFail($interval);
        }
        $interval->fill($data);
        $interval->updatedAt = DbHelper::currTs();
        $interval->save();
        return $interval->toArray();
    }

    /**
     * Delete delivery interval
     * @param $interval
     * @throws \Exception
     */
    public static function deleteDeliveryInterval($interval) {
        if(!$interval instanceof DeliveryIntervalModel) {
            $interval = DeliveryIntervalModel::findOrFail($interval);
        }
        $interval->delete();
    }

    /**
     * Retrive intervals list
     * @param array $params
     *  id - Get interval by id
     * @return array
     */
    public static function getIntervals($params = [])
    {
        $q = DeliveryIntervalModel::orderBy('dayOfWeek', 'asc')
            ->orderBy('startHour');
        if(!empty($params['id'])) {
            $q->where('id', $params['id']);
        }
        return $q->get()->toArray();
    }

    /**
     * Get only active intervals
     */
    public static function getActiveIntervals() {
        return self::getIntervals();
    }

    /**
     * Get interval by id
     * @param $id
     * @return array
     */
    public static function getIntervalById($id) {
        return self::getIntervals(['id' => $id]);
    }
}
