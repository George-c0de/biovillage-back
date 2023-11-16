<?php

namespace Packages\Purchaser\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\OrderModel;
use App\Service\PurchaserBoyService;
use Illuminate\Http\JsonResponse;
use Packages\Purchaser\Http\Requests\IndexRequest;
use Packages\Purchaser\Searchers\PurchaserBoySearcher;
use Packages\Purchaser\Searchers\PurchaserShortageSearcher;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaserController extends BaseApiController {


    /**
     * Products for purchase and total gifts
     * @param \App\Http\Requests\PurchaserBoy\IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request) {
        $params = $request->validated();
        if(empty($params['orderStatus'])) {
            $params['orderStatus'] = OrderModel::STATUS_PLACED;
        }
        $s = new PurchaserBoySearcher();

        return ResponseHelper::success([
            'products' => $s->searchProducts($params),
            'gifts' => $s->searchGifts($params)
        ]);
    }

    public function shortage(IndexRequest $request) {
        $params = $request->validated();
        if(empty($params['orderStatus'])) {
            $params['orderStatus'] = OrderModel::STATUS_PLACED;
        }
        $products = PurchaserShortageSearcher::search($params);
        $gifts = PurchaserShortageSearcher::searchGift($params);

        return ResponseHelper::success([
            'products' => $products,
            'gifts' => $gifts,
        ]);
    }

    /**
     * Products for purchase and total gifts as Excel file
     * @param IndexRequest $request
     * @return StreamedResponse
     */
    public function excel(IndexRequest $request) {
        $params = $request->validated();
        if(empty($params['orderStatus'])) {
            $params['orderStatus'] = OrderModel::STATUS_PLACED;
        }
        $s = new PurchaserBoySearcher();
        $prods = $s->searchProducts($params);
        $gifts = $s->searchGifts($params);

        $f = PurchaserBoyService::exportToExcel($params, $prods, $gifts);
        $resp =  new StreamedResponse(function() use ($f) {
            $f->save('php://output');
        });
        $resp->headers->set('Content-Type', 'application/vnd.ms-excel');
        $resp->headers->set('Content-Disposition', 'attachment;filename="Purchase.xls"');
        $resp->headers->set('Cache-Control','max-age=0');
        return $resp;
    }
}