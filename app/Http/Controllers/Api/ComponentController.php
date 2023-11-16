<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Helpers\Utils;
use App\Http\Requests\Component\ComponentUpdateRequest;
use App\Models\ComponentModel;
use Illuminate\Support\Arr;

/**
 * Контроллер для работы с компонентами
 * Для небольших компонентов на сайте реализован днанный механизм
 * примеры компонентов: контакты, слайдер, отзывы и т.д
 * У компонентов могут быть коллекции (но не обязательно)
 */
class ComponentController extends BaseApiController {
    /**
     * Отдаем список всех доступных для сайта компонентов
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $components = ComponentModel::with('collections')
            ->get();

        return ResponseHelper::success($components->toArray());
    }

    /**
     * Получаем информацию по слагу для конкретного компонента
     * если у компонента есть коллекция то отдаем ее тоже
     * @param $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug) {
        $component = ComponentModel::where('slug', $slug)
            ->with('collections')
            ->first();

        return ResponseHelper::success($component->toArray());
    }

    /**
     * Обновление компонента
     * @param ComponentUpdateRequest $request
     * @param $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ComponentUpdateRequest $request, $slug) {
        $validated = Utils::arrayValuesEmpty($request->validated());
        $component = ComponentModel::where('slug', $slug)->first();
        $component->fill($validated);

        if (Arr::has($validated, 'properties')) {
            $properties = Utils::arrayValuesEmpty($validated['properties']);
            unset($validated['properties']);

            foreach ($properties as $key => $property) {
                $component['properties->'.$key] = $property;
            }
        }

        $component->saveOrFail();

        return ResponseHelper::success($component->toArray());
    }
}
