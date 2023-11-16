<?php namespace App\Http\Controllers\Api;

use App\Service\Images\ImageService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Image\ImageStoreRequest;
use App\Http\Requests\Image\ImageDeleteRequest;
use App\Http\Requests\Image\ImageUpdateRequest;
use Illuminate\Http\JsonResponse;

/**
 * Контроллер для работы с картинками
 */
class ImageController extends BaseApiController {

    /**
     * Отдаем массив картинок по $groupName, $entityId
     * @param $groupName
     * @param $entityId
     * @return JsonResponse
     */
    public function show($groupName, $entityId) {
        $images = ImageService::getToApiFullUrl($groupName, $entityId);
        return ResponseHelper::success($images);
    }


    /**
     * Записываем картинки по $groupName, $entityId
     * @param ImageStoreRequest $request
     * @param $groupName
     * @param $entityId
     * @return JsonResponse
     */
    public function store(ImageStoreRequest $request, $groupName, $entityId) {
        ImageService::save($request->file('images'), $groupName, $entityId);
        $images = ImageService::getToApiFullUrl($groupName, $entityId);

        return ResponseHelper::success($images);
    }


    /**
     * Обновляем картинки по $groupName, $entityId
     * Обновляются только поля не содержащие пути к картинкам
     * например order
     * @param ImageUpdateRequest $request
     * @param $groupName
     * @param $entityId
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(ImageUpdateRequest $request, $groupName, $entityId) {
        $validated = $request->validated();

        ImageService::update($validated['data']);
        $images = ImageService::getToApiFullUrl($groupName, $entityId);

        return ResponseHelper::success($images);
    }

    /**
     * Удаление картинки по $groupName, $entityId
     * @param ImageDeleteRequest $request
     * @param $groupName
     * @param $entityId
     * @return JsonResponse
     */
    public function destroy(ImageDeleteRequest $request, $groupName, $entityId) {
        ImageService::delete($request->ids);

        return ResponseHelper::success([trans('success.image.remove')]);
    }
}
