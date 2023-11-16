<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class RequestHelper {


    /**
     * Запрос пришел из клиентского АПИ
     * @param Request $request
     * @return bool
     */
    public static function isClientApi(Request $request) : bool {
        return $request->is('api/*') || $request->path() == 'api';
    }


    /**
     * Запрос пришел из клиентского АПИ
     * @param Request $request
     * @return bool
     */
    public static function isAdmin(?Request $request = null) : bool {
        if(empty($request)) {
            $request = request();
        }
        return $request->is('admin/*') || $request->path() == 'admin';
    }

    /**
     * На запрос нужно вернуть JSON
     * @param Request $request
     * @return bool
     */
    public static function wantsJson(Request $request) : bool {
        return $request->wantsJson() || $request->ajax();
    }
}