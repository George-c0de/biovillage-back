<?php namespace App\Helpers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Успешный ответ.
     *
     * @param array $result
     * @param int   $status
     *
     * @return JsonResponse
     */
    public static function success($result = [], int $status = Response::HTTP_OK)
    {
        return static::handler([
            'result' => $result,
            'success' => true,
        ], $status);
    }

    /**
     * Частично успешный ответ.
     *
     * @param array $result
     * @param array $errors
     * @param int   $status
     *
     * @return JsonResponse
     */
    public static function done(array $result = [], array $errors = [], int $status = Response::HTTP_PARTIAL_CONTENT)
    {
        return static::handler([
            'result' => $result,
            'errors' => $errors,
            'success' => true
        ], $status);
    }

    /**
     * Успешный ответ с сообщением.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public static function ok(string $message = 'OK')
    {
        return static::success(['message' => $message]);
    }

    /**
     * Ответ с ошибкой.
     *
     * @param array $errors
     * @param int   $status
     *
     * @return JsonResponse
     */
    public static function error(array $errors = [], int $status = Response::HTTP_BAD_REQUEST)
    {
        return static::handler([
            'errors' => $errors,
            'success' => false,
        ], $status);
    }

    /**
     * Текстовая ошибка.
     *
     * @param string $message
     * @param int    $status
     *
     * @return JsonResponse
     */
    public static function errorMessage(string $message, int $status = Response::HTTP_BAD_REQUEST)
    {
        return static::error([
            'message' => [$message],
        ], $status);
    }

    /**
     * Текстовая ошибка с ключом.
     *
     * @param string $key
     * @param string $message
     * @param int $status
     *
     * @return JsonResponse
     */
    public static function errorKey(string $key, string $message, int $status = Response::HTTP_BAD_REQUEST)
    {
        return static::error([
            $key => [$message],
        ], $status);
    }

    /**
     * Общий обработчик ответа.
     *
     * @param array $data
     * @param int   $status
     *
     * @return JsonResponse
     */
    public static function handler(array $data, int $status)
    {
        return response()->json(
            Utils::camelArr($data),
            $status,
            [
                'Content-Type' => 'application/json;charset=UTF-8',
                'Charset' => 'utf-8',
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
}
