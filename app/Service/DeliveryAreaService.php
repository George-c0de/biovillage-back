<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Models\DeliveryAreaModel;
use App\Models\DeliveryIntervalModel;
use App\Models\TagModel;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 *
 * Work with delivery intervals
 *
 * @package App\Service\DeliveryIntervalService
 */
class DeliveryAreaService extends BaseService
{

    /**
     * Create new delivery interval
     * @param $data
     *  names -
     *  prices -
     *  polygons -
     *  colors -
     * @return array
     */
    public static function replace($data)
    {
        DeliveryAreaModel::query()->delete();
        foreach(range(0, count($data['names'])-1) as $i) {
            $da = new DeliveryAreaModel();
            $da->name = $data['names'][$i];
            $da->price = $data['prices'][$i];
            $poly = str_replace(' ', '', $data['polygons'][$i]);
            $da->polygon = Db::raw('polygon(\'' . $poly . '\')');
            $da->color = $data['colors'][$i];
            $da->deliveryFreeSum = $data['deliveryFreeSums'][$i];
            $da->active = $data['actives'][$i] ?? true;

            $da->save();
        }
    }

    /**
     * Get all delivery areas
     */
    public static function getAreas() {
        return DeliveryAreaModel::orderBy('name')->get()->toArray();
    }

    /**
     * Get delivery areas with parsed polygon field
     */
    public static function getAreasPolygonParsed() {
        return array_map(function($area) {
            $i = null;
            $area['poly'] = DbHelper::pgArrayToArray($area['polygon'], 0, $i, '(', ')');
            unset($area['polygon']);
            return $area;
        }, static::getAreas());
    }

    /**
     * Show data for one delivery area
     * @param $areaId
     * @return
     */
    public static function getArea($areaId) {
        return DeliveryAreaModel::findOrFail($areaId)->toArray();
    }

    /**
     * Update area
     * @param $areaId
     * @param $data
     * @return
     */
    public static function update($areaId, $data) {
        $area = DeliveryAreaModel::find($areaId);
        $area->fill($data);
        $area->save();

        return self::getArea($areaId);
    }
}
