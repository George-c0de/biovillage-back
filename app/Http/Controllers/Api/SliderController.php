<?php

namespace App\Http\Controllers\Api;

use App\Models\SliderModel;
use App\Service\SliderService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Service\Images\ImageService;
use Illuminate\Support\Collection;
use App\Http\Requests\Slider\{StoreRequest, UpdateRequest, ShowOrDestroyRequest};

/**
 * Slider Controller
 *
 * @package App\Http\Controllers\Api
 */
class SliderController extends Controller
{
    /**
     * Get all slides
     *
     * @return JsonResponse
     */
    public function index()
    {
        return ResponseHelper::success(
            SliderService::search()
        );
    }

    /**
     * Get single slide
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return mixed
     */
    public function show(ShowOrDestroyRequest $request)
    {
        return ResponseHelper::success(
            SliderService::getById($request->validated()['id'])
        );
    }

    /**
     * Add new slide
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $slider = SliderModel::create($request->validated());
        ImageService::save(
            $request->file('image'),
            SliderModel::GROUP_NAME,
            $slider->id
        );

        return ResponseHelper::success(SliderService::getById($slider->id));
    }

    /**
     * Update slide
     *
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $slider = SliderModel::find($validated['id']);
        $ignoreKeys = ['id', 'image'];

        if (isset($validated['image'])) {
            $getSliderImages = ImageService::getToApi(SliderModel::GROUP_NAME, $validated['id']);
            $getSliderImagesIDs = Collection::make($getSliderImages)->pluck('id');
            ImageService::delete($getSliderImagesIDs->toArray());
            ImageService::save(
                $request->file('image'),
                SliderModel::GROUP_NAME,
                $validated['id']
            );
        }

        foreach ($validated as $key => $value) {
            if (!in_array($key, $ignoreKeys)) {
                $slider->$key = $value;
            }
        }

        $slider->save();

        return ResponseHelper::success(SliderService::getById($slider->id));
    }

    /**
     * Destroy slide
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     */
    public function destroy(ShowOrDestroyRequest $request)
    {
        $sliderId = $request->validated()['id'];

        $slider = SliderModel::find($sliderId);
        if(!empty($slider)) {
            $slider->delete();
        }

        return ResponseHelper::success(['Slider successfully deleted!']);
    }
}
