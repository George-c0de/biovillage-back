<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Helpers\Utils;
use App\Http\Requests\Component\ComponentCollectionStoreRequest;
use App\Http\Requests\Component\ComponentCollectionUpdateRequest;
use App\Models\ComponentCollectionModel;
use App\Models\ComponentModel;
use App\Service\StorageService;
use Illuminate\Support\Facades\Storage;

/**
 * Коллекция для компонентов
 */
class ComponentCollectionController extends BaseApiController {
    /**
     * Запись коллекции в компонент
     * @param ComponentCollectionStoreRequest $request
     * @param $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ComponentCollectionStoreRequest $request, $slug) {
        $validated = Utils::arrayValuesEmpty($request->validated());

        $component = ComponentModel::where('slug', $slug)->first();
        $componentCollection = new ComponentCollectionModel;
        // Если в принимаемых данных есть поля с файлами то записываем их
        foreach ($validated as $key => $value) {
            if ($request->hasFile($key)) {
                $value = StorageService::saveMd5($request->file($key), 'components/'.$slug);
            }
            $componentCollection['properties->'.$key] = $value;
        }
        $component->collections()->save($componentCollection);

        $component = ComponentModel::where('slug', $slug)
            ->with('collections')
            ->first();

        return ResponseHelper::success($component->toArray());
    }

    /**
     * Обновление коллекции в компоненте
     * @param ComponentCollectionUpdateRequest $request
     * @param $slug
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ComponentCollectionUpdateRequest $request, $slug, $id) {
        $validated = $request->validated();

        $component = ComponentModel::where('slug', $slug)->first();
        $componentCollection = $component->collections()->where('id', $id)->first();
        // Если в принимаемых данных есть поля с файлами то удаляем старые и записываем новые
        foreach ($validated as $key => $value) {
            if ($request->hasFile($key)) {
                if (Storage::disk('public')->exists($value)) {
                    Storage::disk('public')->delete($componentCollection->properties[$key]);
                }
                $value = StorageService::saveMd5($request->file($key), 'components/'.$slug);
            }
            $componentCollection['properties->'.$key] = $value;
        }
        $componentCollection->save();

        $component = ComponentModel::where('slug', $slug)
            ->with('collections')
            ->first();

        return ResponseHelper::success($component->toArray());
    }

    /**
     * Удаление элемента коллекции в компоненте
     * @param $slug
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($slug, $id) {
        $component = ComponentModel::where('slug', $slug)->first();
        $componentCollection = $component->collections()->where('id', $id)->first();
        $properties = $componentCollection->properties;
        // Удаляем файлы с хранилица если они есть
        foreach ($properties as $key => $value) {
            if (Storage::disk('public')->exists($value)) {
                Storage::disk('public')->delete($componentCollection->properties[$key]);
            }
        }
        $componentCollection->delete();

        $component = ComponentModel::where('slug', $slug)
            ->with('collections')
            ->first();

        return ResponseHelper::success($component->toArray());
    }
}
