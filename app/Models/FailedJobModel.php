<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use App\Models\Auth\Client;

/**
 * Работа с заданиями очереди, которые завершились с ошибками
 * @package App
 */

class FailedJobModel extends BaseModel {
    protected $connection= 'main';
    protected $table = 'failedJobs';
}
