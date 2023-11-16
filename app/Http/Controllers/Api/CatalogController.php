<?php

namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Catalog\IndexRequest;
use App\Http\Requests\Product\SearchPromoRequest;
use App\Models\ProductModel;
use App\Service\GroupService;
use App\Service\ProductService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;


class CatalogController extends BaseApiController {

    /**
     * Search products in catalog
     * Use only clients
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \ReflectionException
     */
    public function index(IndexRequest $request) {

        // Search groups
        $params['onlyActive'] = true;
        if($request->has('name')) {
            $params['name'] = $request->name;
            // Sort sim
            $params['sort'] = ProductModel::SORT_SIM;
            $params['sortDirect'] = 'desc';
        } else {
            // Sort default
            $params['sort'] = ProductModel::SORT_ORDER;
            $params['sortDirect'] = 'asc';
        }

        $groups = [];
        if(!$request->has('groupId')) {
            if($request->has('name')) {
                $groups = GroupService::search($params);
            }
        }

        // Search products
        if($request->has('tags')) {
            $params['tags'] = $request->validatedAttribute('tags');
        }
        if($request->has('groupId')) {
            $params['groupId'] = $request->validatedAttribute('groupId');
        }

        // Pagination
        $pager = new Paginator(
            ProductService::getCount($params),
            $request->validatedAttribute('perPage', ProductModel::DEFAULT_PER_PAGE),
            $request->validatedAttribute('page', 1)
        );
        $params['limit'] = $pager->getLimit();
        $params['offset'] = $pager->getOffset();
        $products = ProductService::search($params);

        return ResponseHelper::success([
            'groups' => $groups,
            'products' => $products,
            'prodPager' => $pager->toArray(),
        ]);
    }

    /**
     * Search promo products
     * @param SearchPromoRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \ReflectionException
     */
    public function promo(SearchPromoRequest $request) {

        $params = $request->validated();

        // Search products
        $params['onlyPromotion'] = true;
        $params['onlyActive'] = true;

        $params['sort'] = Arr::has($params, 'sort') && $params['sort'] ? $params['sort'] : ProductModel::SORT_NAME;
        $params['sortDirect'] = Arr::has($params, 'sortDirect') && $params['sortDirect'] ? $params['sortDirect'] : 'asc';
        $params['perPage'] = Arr::has($params, 'perPage') && $params['perPage'] ? $params['perPage'] : ProductModel::DEFAULT_PER_PAGE;
        $params['page'] = Arr::has($params, 'page') && $params['page'] ? $params['page'] : 1;

        $pager = new Paginator(
            ProductService::getCount($params),
            $params['perPage'],
            $params['page']
        );
        $params['limit'] = $pager->getLimit();
        $params['offset'] = $pager->getOffset();
        $products = ProductService::search($params);
        // For promo groups another ID
        $products = array_map(function ($prod) {
            $prod['groupId'] = ProductModel::GROUP_ID_PROMO;
            return $prod;
        }, $products);

        return ResponseHelper::success([
            'products' => $products,
            'prodPager' => $pager->toArray(),
        ]);
    }
}
