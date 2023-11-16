<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;

/**
 * Plug-controller
 */
class PlugController extends BaseApiController
{
    /**
     * Plug for non-existent route.
     *
     * @return JsonResponse
     */
    public function anyRoute()
    {
        return ResponseHelper::errorMessage('API URL not in use', Response::HTTP_NOT_FOUND);
    }

    /**
     * Plug for `login` named route `/login`
     *
     * @return string
     */
    public function loginRoute()
    {
        return 'You need to be authorized!';
    }

}
