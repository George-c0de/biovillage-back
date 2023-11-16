<?php namespace App\Http\Controllers\Api;

use App\Http\Requests\Unit\ShowOrDestroyRequest;
use App\Models\UnitModel;
use App\Helpers\ResponseHelper;
use App\Http\Requests\CheckIdRequest;
use App\Http\Requests\Unit\StoreRequest;
use App\Http\Requests\Unit\UpdateRequest;
use Illuminate\Http\JsonResponse;

/**
 * Unit Controller.
 *
 * @package App\Http\Controllers\Api
 */
class UnitController extends BaseApiController
{
    /**
     * Get all units.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $units = UnitModel::orderBy('shortName')->get();

        return ResponseHelper::success($units->toArray());
    }

    /**
     * Get unit.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     */
    public function show(ShowOrDestroyRequest $request)
    {
        $validated = $request->validated();
        return ResponseHelper::success(
            UnitModel::find($validated['id'])
        );
    }

    /**
     * Add new unit.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        return ResponseHelper::success(
            UnitModel::create($request->validated())->toArray()
        );
    }

    /**
     * Update exists unit.
     *
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $unit = UnitModel::find($validated['id']);
        unset($validated['id']);
        $unit->fill($validated);
        $unit->save();

        return ResponseHelper::success($unit->toArray());
    }

    /**
     * Delete exists unit.
     *
     * @param ShowOrDestroyRequest $request
     *
     * @return JsonResponse
     */
    public function destroy(ShowOrDestroyRequest $request)
    {
        $validated = $request->validated();

        $unit = UnitModel::find($validated['id']);
        if(!empty($unit)) {
            $unit->delete();
        }

        return ResponseHelper::success([trans('success.unit.remove')]);
    }
}
