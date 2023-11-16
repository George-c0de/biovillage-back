<?php namespace App\Http\Controllers\Api;

use App\Models\TagModel;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Tag\{StoreRequest, UpdateRequest, ShowOrDestroyRequest};

/**
 * Tag Controller.
 *
 * @package App\Http\Controllers\Api
 */
class TagController extends BaseApiController
{
    /**
     * List of all tags.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $tags = TagModel::orderBy('order')
            ->orderBy('name')
            ->get();
        return ResponseHelper::success($tags->toArray());
    }

    /**
     * Add new tag.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        return ResponseHelper::success(
            TagModel::create($request->validated())
        );
    }

    /**
     * Show single tag.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     */
    public function show(ShowOrDestroyRequest $request)
    {
        return ResponseHelper::success(
            TagModel::findOrFail($request->validated()['id'])
        );
    }

    /**
     * Update existing tag.
     *
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $tag = TagModel::findOrFail($validated['id']);
        unset($validated['id']);

        foreach ($validated as $key => $value) {
            $tag->$key = $value;
        }

        $tag->save();

        return ResponseHelper::success($tag);
    }

    /**
     * Delete existing tag.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     */
    public function destroy(ShowOrDestroyRequest $request)
    {
        $validated = $request->validated();

        $tag = TagModel::find($validated['id']);
        $tag->delete();

        // Удаляем тег из продуктов
        DB::statement("update products set tags = tags - '{".$validated['id']."}'::int[];");

        return ResponseHelper::success([trans('success.tag.remove')]);
    }
}
