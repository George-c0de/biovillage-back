<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Models\PickPointModel;


class PickpointService {

    /**
     * Get all active pickpoints
     * @param $isActive
     * @return PickPointModel[]
     */
    public static function getAll() {
       return PickPointModel::whereNull('deletedAt')->get();
    }

    /**
     * Create new pickpoint
     * @param array $data
     * @return PickPointModel
     */
    public static function add($data) {
        $a = new PickPointModel($data);
        $a->save();

        return $a;
    }

    /**
     * Search single pickpoint
     * @param $id
     * @return PickPointModel
     */
    public static function searchOne($id) {
       return PickPointModel::findOrFail($id);
    }

    /**
     * Delete pickpoint
     * @param $id
     * @return null
     */
    public static function delete($id) {
        PickPointModel::whereId($id)->first()->delete();
    }

    /**
     * Update pickpoint
     * @param $id
     * @param $params
     */
    public static function update($id, $params) {

        $pickpoint = self::searchOne($id);
        $pickpoint->fill($params);
        $pickpoint->save();

        return $pickpoint;
    }


}
