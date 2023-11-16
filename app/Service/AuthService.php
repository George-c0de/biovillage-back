<?php

namespace App\Service;

use App\Models\Auth\Admin;
use App\Models\Auth\Client;
use Illuminate\Support\Facades\Auth;

class AuthService {

    /**
     * Отдаем клиента если запрос делает клиент
     * Отдаем инфу по клиенту по id если запрашивает админ
     * Нужно для универсальных контролерров которые дают доступ к измению данных для клиентов и админов
     *
     * @param bool $id
     * @param $select
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function dynamicClient($id = false, $select = '*') {
        $client = false;
        if($id && Auth::user() instanceof Admin){
            $client = Client::select($select)->where('id', $id)->first();
        } else if(Auth::user() instanceof Client) {
            $client = Auth::user();
        }

        return $client;
    }

    /**
     * Проверка на клиента
     * @param $client
     * @param $role
     * @return bool
     */
    public static function isClient($client, $role = false){
        if ($role) {
            if($client instanceof Client && $client['role'] == $role) return true;
        } else {
            if ($client instanceof Client && !$role) return true;
        }

        return false;
    }

    /**
     * Проверка на админа
     * @param $client
     * @param $roles array
     * @return bool
     */
    public static function isAdmin($client, $roles = false){
        if ($roles) {
            if($client instanceof Admin && AdminRole::hasRole($client, $roles)) return true;
        } else {
            if ($client instanceof Admin && !$roles) return true;
        }

        return false;
    }
}
