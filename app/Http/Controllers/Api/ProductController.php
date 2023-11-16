<?php namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Models\ProductModel;
use App\Helpers\DbHelper;
use App\Helpers\ResponseHelper;
use App\Searchers\ProductSearcher;
use App\Service\ProductService;
use Illuminate\Http\JsonResponse;
use App\Service\Images\ImageService;
use App\Http\Requests\Product\SearchRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\ShowOrDestroyRequest;
use App\Http\Requests\Product\UpdateRequest;
use Illuminate\Support\Arr;

/**
 * Product Controller.
 *
 * @package App\Http\Controllers\Api
 */
class ProductController extends BaseApiController
{

    /**
     * Search products.
     *
     * @param SearchRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function index(SearchRequest $request)
    {
        $params = $request->validated();

        $pager = new Paginator(
            ProductService::getCount($params),
            $params['perPage'] ?? ProductModel::DEFAULT_PER_PAGE,
            $params['page'] ?? 1
        );
        $params['limit'] = $pager->getLimit();
        $params['offset'] = $pager->getOffset();

        return ResponseHelper::success([
            'products' => ProductService::search($params),
            'pager' => $pager->toArray()
        ]);
    }

    /**
     * Store product item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function store(StoreRequest $request)
    {
        return ResponseHelper::success(
            ProductService::createProduct($request->validated())
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
        $validated = $request->validated();
        return ResponseHelper::success(
            ProductService::searchProductById($validated['id'])
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
        $validated = $request->validated();
        return ResponseHelper::success(
            ProductService::updateProduct($validated['id'], $validated)
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
        ProductService::delProduct($request->id);
        return ResponseHelper::success([trans('success.product.remove')]);
    }

}
