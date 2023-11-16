<?php namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Models\ProductModel;
use App\Helpers\DbHelper;
use App\Helpers\ResponseHelper;
use App\Searchers\ProductSearcher;
use App\Service\GiftService;
use Illuminate\Http\JsonResponse;
use App\Service\Images\ImageService;
use App\Http\Requests\Gift\IndexRequest;
use App\Http\Requests\Gift\StoreRequest;
use App\Http\Requests\Gift\ShowOrDestroyRequest;
use App\Http\Requests\Gift\UpdateRequest;
use Illuminate\Support\Facades\App;

/**
 * Gift Controller.
 *
 * @package App\Http\Controllers\Api
 */
class GiftController extends BaseApiController
{

    /**
     * @var $gifts - Gift service
     */
    private $gifts;

    /**
     * GiftController constructor.
     */
    public function __construct()
    {
        $this->gifts =  App::make('Gifts');
    }

    /**
     * Search products.
     *
     * @param IndexRequest $request
     *
     * @param GiftService $service
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function index(IndexRequest $request)
    {
        return ResponseHelper::success(
            resolve('Gifts')->search($request->validated())
        );
    }

    /**
     * Return public gift list
     * @param GiftService $service
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function publicList() {
        return ResponseHelper::success(
            $this->gifts->search([
                'onlyActive' => true,
                'sort' => 'order'
            ])
        );
    }

    /**
     * Store gift item.
     *
     * @param StoreRequest $request
     *
     * @param GiftService $service
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function store(StoreRequest $request)
    {
        return ResponseHelper::success(
            $this->gifts->createGift($request->validated())
        );
    }

    /**
     * Get product info.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @param GiftService $service
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function show(ShowOrDestroyRequest $request)
    {
        return ResponseHelper::success(
            $this->gifts->searchGiftById($request->id)
        );
    }

    /**
     * Update product.
     *
     * @param UpdateRequest $request
     *
     * @param GiftService $service
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        return ResponseHelper::success(
            $this->gifts->updateGift($validated['id'], $validated)
        );
    }

    /**
     * Delete gift
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(ShowOrDestroyRequest $request)
    {
       $this->gifts->delete($request->id);
        return ResponseHelper::ok();
    }



}
