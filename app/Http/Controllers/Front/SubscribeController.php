<?php namespace App\Http\Controllers\Front;

use App\Helpers\DbHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Subscribe\SubscribeRequest;
use App\Models\SubscribeModel;
use App\Service\SubscribeService;
use Illuminate\Http\JsonResponse;
use App\Service\Images\ImageService;
use App\Http\Requests\Groups\StoreRequest;
use App\Http\Requests\Groups\UpdateRequest;
use App\Http\Requests\Groups\ShowOrDestroyRequest;

/**
 * Регистрация клиента на почтовую рассылку
 *
 * @package App\Http\Controllers\Front
 */
class SubscribeController extends BaseFrontController
{
    /**
     * Subscribe
     *
     * @param SubscribeRequest $request
     * @return JsonResponse
     */
    public function index(SubscribeRequest $request)
    {
        SubscribeService::subscribe($request->toArray());
        SubscribeService::sendPromo($request->email);
        return ResponseHelper::ok();
    }
}
