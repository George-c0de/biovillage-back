<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BaseModel extends Model {

    /**
     * Метод возвращает название таблицы для модели
     * @return string
     */
    public static function getTableName() {
        return with(new static)->getTable();
    }
}
