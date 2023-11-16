<?php namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Helpers\Utils;
use App\Models\ProductModel;
use App\Helpers\DbHelper;
use App\Helpers\ResponseHelper;
use App\Models\SettingsModel as SM;
use App\Searchers\ProductSearcher;
use App\Service\DeliveryAreaService;
use App\Service\KmlMapService;
use App\Service\SettingsService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\DeliveryArea\LoadKmlRequest;
use App\Http\Requests\DeliveryArea\StoreRequest;
use App\Http\Requests\DeliveryArea\ShowRequest;
use App\Http\Requests\DeliveryArea\UpdateRequest;

/**
 * Delivery Area
 *
 * @package App\Http\Controllers\Api
 */
class DeliveryAreaController extends BaseApiController
{


    /**
     * Load KML file and parse delivery areas
     * @param LoadKmlRequest $request
     * @return JsonResponse
     */
    public function loadKml(LoadKmlRequest $request) {

        $s = new KmlMapService();
        try {
            $areas = $s->parseKmlFile($request->file('kmlFile')->getRealPath());
        }
        catch (\Exception $e) {
            return ResponseHelper::errorMessage(trans('errors.daCantLoadKml'));
        }

        return ResponseHelper::success(array_map(function($area) {
            $area['polygon'] = DbHelper::arrayToPgPath($area['polygon']);
            $area['active'] = true;
            $area['deliveryFreeSum'] = SettingsService::getSettingValue(
                SM::SETTING_DELIVERY_FREE_SUM);
            return $area;
        }, $areas));
    }

    /**
     * Get all delivery areas.
     * Polygons as string
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function index()
    {
        return ResponseHelper::success(
            DeliveryAreaService::getAreas()
        );
    }

    /**
     * Get all delivery areas
     * Polygon as array of vertex
     */
    public function indexPolygonParsed() {
        return ResponseHelper::success(
            DeliveryAreaService::getAreasPolygonParsed()
        );
    }

    /**
     * Store gift item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function store(StoreRequest $request)
    {

        DeliveryAreaService::replace($request->toArray());
        return ResponseHelper::success(
            DeliveryAreaService::getAreas()
        );
    }

    /**
     * Get product info.
     *
     * @param ShowRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function show(ShowRequest $request)
    {
        return ResponseHelper::success(
            DeliveryAreaService::getArea($request->id)
        );
    }

    /**
     * Update delivery area
     *
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        return ResponseHelper::success(
            DeliveryAreaService::update($validated['id'], $validated)
        );
    }

}
