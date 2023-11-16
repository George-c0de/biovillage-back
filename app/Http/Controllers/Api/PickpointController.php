<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Requests\PickPoint\StoreRequest;
use App\Http\Requests\PickPoint\UpdateRequest;
use App\Service\PickpointService;


class PickpointController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ResponseHelper::success(
            PickpointService::getAll()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $pickpoint = PickpointService::add($request->validated());

        return ResponseHelper::success(
            $pickpoint
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pickpoint = PickpointService::searchOne($id);

            return ResponseHelper::success(
                $pickpoint
            );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        PickpointService::update($id, $request->validated());
        return ResponseHelper::success(
            PickpointService::searchOne($id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PickpointService::delete($id);
        return ResponseHelper::ok();
    }
}
