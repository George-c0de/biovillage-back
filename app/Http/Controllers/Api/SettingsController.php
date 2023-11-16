<?php namespace App\Http\Controllers\Api;

use App\Http\Requests\Settings\ShowRequest;
use App\Http\Requests\Settings\UpdateRequest;
use App\Models\SettingsModel;
use App\Service\SettingsService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Back\BaseBackController;

/**
 * Settings API Controller
 *
 * @package App\Http\Controllers\Api
 */
class SettingsController extends BaseBackController
{

    /**
     * Get one setting.
     *
     * @param ShowRequest $request
     * @return JsonResponse
     */
    public function index(ShowRequest $request) {
        return ResponseHelper::success(
            SettingsService::getSettingValue(
                $request->validatedAttribute('key')
            )
        );
    }

    /**
     * Update settings
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        SettingsService::updateSettings($request->validated());
        return ResponseHelper::success(
            SettingsService::getSettings(true)
        );
    }

    /**
     * Show full settings for mobile clients
     */
    public function clientSettings() {
        return ResponseHelper::success(
            SettingsService::getSettingsForClient()
        );
    }
}
