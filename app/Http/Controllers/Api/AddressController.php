<?php namespace App\Http\Controllers\Api;

use App\Models\AddressModel;
use App\Helpers\ResponseHelper;
use App\Service\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Address\{StoreRequest, ShowOrDestroyRequest};

/**
 * Address Controller.
 *
 * @package App\Http\Controllers\Api
 */
class AddressController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return ResponseHelper::success(
            AddressService::searchClientAddresses(Auth::user())
        );
    }

    /**
     * Store a newly created address in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $a = AddressService::addAddress(Auth::user(), $request->validated());
        return ResponseHelper::success(
            AddressService::searchOne($a->id, Auth::user())
        );
    }

    /**
     * Display the address resource.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     */
    public function show(ShowOrDestroyRequest $request)
    {
        return ResponseHelper::success(
            AddressService::searchOne($request->id, Auth::user())
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     */
    public function destroy(ShowOrDestroyRequest $request)
    {
        AddressService::delete(Auth::user(), $request->id);
        return ResponseHelper::success([trans('success.address.remove')]);
    }
}
