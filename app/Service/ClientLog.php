<?php

namespace App\Service;

use App\Models\Auth\Client;
use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\ClientLogModel;
use Illuminate\Support\Facades\Auth;


/**
 * Создание новой записи в логе.
 * Тип записи определяется названием вызываемого статического метода.
 * Единственный параметр должен быть массивом, он записывается в поле json.
 * Некоторые элементы массива имеют специальное назначение:
 * userId - обозначает, что запись в лог имеет отношение к указанной учетной записи;
 * cityId - обозначает, что запись в лог имеет отношение к указанному городу.
 * @see LogModel
 */

class ClientLog extends BaseService {

    public static function __callStatic($method, $parameters)
    {

        // Определим к какому клиенту относится запись лога
        $data = $parameters[0];
        if($data instanceof Client) {
            $data = [
                'clientId' => $data->id
            ];
        }
        $clientId = $data['clientId']
            ?? $data['client_id']
            ?? optional($data['client'] ?? null)->id
            ?? optional(Auth::guard('client')->user())->id;
        if(empty($clientId)){
            Utils::raise('No client id');
        }
        unset($data['clientId']);
        unset($data['client_id']);

        // Если есть админ, то добавим его к параметрам
        $admin = Auth::guard('admin')->user();
        if(!empty($admin)) {
            $data['__admin_id'] = $admin->id;
            $data['__admin_name'] = $admin->name;
        }

        // Сохраним имя клиента
        $data['__client_name']  = $data['clientName']
            ?? $data['client_name']
            ?? optional($data['client'] ?? null)->name
            ?? optional(Auth::guard('client')->user())->name;

        // Сохраним
        $rec = new ClientLogModel();
        $rec->clientId = $clientId;
        $rec->type = strtolower(snake_case($method));
        $rec->data = json_encode($data);
        $rec->dtAdd = DbHelper::currTs();
        $rec->save();
    }
}