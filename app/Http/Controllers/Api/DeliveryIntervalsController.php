<?php namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Models\ProductModel;
use App\Helpers\DbHelper;
use App\Helpers\ResponseHelper;
use App\Searchers\ProductSearcher;
use App\Service\DeliveryIntervalService;
use App\Service\ProductService;
use Illuminate\Http\JsonResponse;
use App\Service\Images\ImageService;
use App\Http\Requests\DeliveryIntervals\StoreRequest;
use App\Http\Requests\DeliveryIntervals\ShowOrDestroyRequest;
use App\Http\Requests\DeliveryIntervals\UpdateRequest;

/**
 * Product Controller.
 *
 * @package App\Http\Controllers\Api
 */
class DeliveryIntervalsController extends BaseApiController
{

    /**
     * Show all delivery intervals
     *
     * @return JsonResponse
     */
    public function index()
    {
        return ResponseHelper::success(
            DeliveryIntervalService::getIntervals()
        );
    }

    /**
     * Store delivery interval
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function store(StoreRequest $request)
    {
        return ResponseHelper::success(
            DeliveryIntervalService::addDeliveryInterval(
                $request->validated()
            )->toArray()
        );
    }

    /**
     * Get product info.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function show(ShowOrDestroyRequest $request)
    {
        return ResponseHelper::success(
            DeliveryIntervalService::getIntervalById($request->id)
        );
    }

    /**
     * Update product.
     *
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function update(UpdateRequest $request)
    {
        return ResponseHelper::success(
            DeliveryIntervalService::editDeliveryInterval(
                $request->id, $request->validated()
            )
        );
    }

    /**
     * Delete product.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(ShowOrDestroyRequest $request)
    {
        DeliveryIntervalService::deleteDeliveryInterval(
            $request->id
        );
        return ResponseHelper::ok();
    }

}
