<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use App\Models\Auth\Client;

/**
 * Модель для записи клиентских логов
 * Class ClientLogModel
 * @package App
 */

class ClientLogModel extends BaseModel {
    protected $connection= 'log';
    protected $table = 'clientLog';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $dates = [
        'createdAt',
        'updatedAt'
    ];


    /**
     * Отформатированные данные логов
     * @return string
     */
    public function prettyJson(): string {
        return json_encode(
            json_decode($this->data),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Возвращаем сформированный запрос логов
     * @param array $where
     * @return
     */
    public static function queryLogs(array $where = []) {
        return
            self::select([
                'client_log.*',
                'client_log.data->__client_name as client_name',
                'client_log.data->__admin_id AS admin_id',
                'client_log.data->__admin_name AS admin_name',
            ])
                ->where($where)
                ->orderBy('client_log.dt_add', 'desc');
    }
}
