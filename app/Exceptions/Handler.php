<?php namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\RequestHelper;
use App\Mail\ExceptionOccured;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Logging all exceptions
     * @var array
     */
    protected $internalDontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable  $exception
     *
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {

        parent::report($exception);
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     *
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        // На запрос нужно отдать json
        $needJson = RequestHelper::wantsJson($request);

        // Запрос из АПИ
        $isApi = RequestHelper::isClientApi($request);

        // Незнаю почему, но при ошибке логина возникает AuthenticationException
        // и Laravel отдать 500 взамен 401. Исправим это.
        if ($exception instanceof AuthenticationException && $isApi) {
            return ResponseHelper::errorMessage('Unauthenticated', Response::HTTP_UNAUTHORIZED);
        }

        // У админа нет доступа к разделу сайта
        if ($exception instanceof AuthorizationException) {
            return ResponseHelper::errorMessage('You have no rights.', Response::HTTP_FORBIDDEN);
        }

        if ($needJson || $isApi) {
            $errDetail = [];
            if (App::environment(['local', 'test'])) {
                $errDetail = [
                    'trance' => $exception->getTraceAsString(),
                    'message' => $exception->getMessage(),
                    'line' => $exception->getLine(),
                    'file' => $exception->getFile()
                ];
            } else {
                if($exception instanceof ProjectException) {
                    $errDetail['error'] = $exception->getMessage();
                }
            }

            return ResponseHelper::error($errDetail, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return RedirectResponse|JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
        if (RequestHelper::isClientApi($request) || RequestHelper::wantsJson($request)) {
            return ResponseHelper::errorMessage('Unauthenticated', Response::HTTP_UNAUTHORIZED);
        }

        $guard = Arr::get($exception->guards(), 0);
        $route = '';
        if ($guard === 'admin') {
            $route = 'adminLogin';
        } else if ($guard === 'client') {
            $route = 'login';
        }

        return redirect()->guest(route($route));
    }

}
