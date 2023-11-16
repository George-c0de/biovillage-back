<?php namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Models\GroupModel;
use App\Service\GroupService;
use Illuminate\Http\JsonResponse;
use App\Service\Images\ImageService;
use App\Http\Requests\Groups\StoreRequest;
use App\Http\Requests\Groups\UpdateRequest;
use App\Http\Requests\Groups\ShowOrDestroyRequest;

/**
 * Products group controller.
 *
 * @package App\Http\Controllers\Api
 */
class GroupController extends BaseApiController
{
    /**
     * Get all groups.
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function index()
    {
        return ResponseHelper::success(
            GroupService::search()
        );
    }

    /**
     * Get single section.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function show(ShowOrDestroyRequest $request)
    {
        $validated = $request->validated();
        return ResponseHelper::success(
            GroupService::getById($validated['id'])
        );
    }

    /**
     * Add section.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function store(StoreRequest $request)
    {
        $group = GroupService::createGroup($request->validated());
        return ResponseHelper::success(
            GroupService::getById($group->id)
        );
    }

    /**
     * Update group
     *
     * @param UpdateRequest $request
     * @param $groupId
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function update(UpdateRequest $request, $groupId)
    {
        GroupService::updateGroup($groupId, $request->validated());
        return ResponseHelper::success(
            GroupService::getById($groupId)
        );
    }

    /**
     * Delete a group
     *
     * @param ShowOrDestroyRequest $request
     * @return JsonResponse
     */
    public function delete(ShowOrDestroyRequest $request) {
        $validated = $request->validated();
        $group = GroupModel::find($validated['id']);
        if(!empty($group)) {
            $group->delete();
        }
        return ResponseHelper::ok();
    }

}
