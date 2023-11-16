<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Helpers\Utils;
use App\Http\Requests\Seo\SeoStoreRequest;
use App\Http\Requests\Seo\SeoUpdateRequest;
use App\Models\SeoModel;

/**
 * Контроллер для работы с сео
 */
class SeoController extends BaseApiController {
    /**
     * Отдаем сео настройки для определенной страницы по $groupName, $entityId
     * @param $groupName
     * @param $entityId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($groupName, $entityId) {
        $seo = SeoModel::where([
            ['groupName', $groupName],
            ['entityId', $entityId],
        ])->first();

        if (!$seo) {
            return ResponseHelper::errorMessage('Данной сущности не существует');
        }

        return ResponseHelper::success($seo->toArray());
    }

    /**
     * Записываем сео настройки для определенной страницы по $groupName, $entityId
     * @param SeoStoreRequest $request
     * @param $groupName
     * @param $entityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SeoStoreRequest $request, $groupName, $entityId) {
        $validated = Utils::arrayValuesEmpty($request->validated());

        $seo = SeoModel::updateOrCreate(
            [
                'groupName'=> $groupName,
                'entityId'=> $entityId,
            ], $validated
        );

        return ResponseHelper::success($seo->toArray());
    }

    /**
     * Обновляем сео настройки для определенной страницы по $groupName, $entityId
     * @param SeoUpdateRequest $request
     * @param $groupName
     * @param $entityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SeoUpdateRequest $request, $groupName, $entityId) {
        $validated = Utils::arrayValuesEmpty($request->validated());

        $seo = SeoModel::updateOrCreate(
            [
                'groupName'=> $groupName,
                'entityId'=> $entityId,
            ], $validated
        );

        return ResponseHelper::success($seo->toArray());
    }


    /**
     * Удаляем сео настройки для определенной страницы по $groupName, $entityId
     * @param $groupName
     * @param $entityId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($groupName, $entityId) {
        $seo = SeoModel::where([
            ['groupName', $groupName],
            ['entityId', $entityId],
        ])->first();
        if (!$seo) {
            return ResponseHelper::errorMessage('Данной сущности не существует');
        }
        $seo->delete();

        return ResponseHelper::ok();
    }
}
