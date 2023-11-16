<?php

namespace App\Http\Controllers\Back;

use App\Helpers\DbHelper;
use App\Http\Requests\Admins\AddRequest;
use App\Http\Requests\Admins\DelRequest;
use App\Http\Requests\Admins\PasswordRequest;
use App\Http\Requests\Admins\ProfileRequest;
use App\Http\Requests\Admins\RolesRequest;
use App\Http\Requests\Admins\GetRequest;
use App\Models\Auth\RolesAndRights;
use App\Models\ClientLogModel;
use App\Service\ClientLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;


class ClientLogsController extends BaseBackController {


    // Число выводимых логов на одной странице
    const LOGS_PER_PAGE = 15;

    /**
     * Страница с записями лога.
     * @return Response
     */
    public function index() {
        return view('back.log.index', [
            'logs' => ClientLogModel::queryLogs()->paginate(self::LOGS_PER_PAGE),
        ]);
    }

    /**
     * Страница записи лога.
     * @param $id Ид. записи
     * @return Response
     */
    public function show($id) {
        return view('back.log.show', [
            'log' => ClientLogModel::queryLogs(['id' => $id])->first()
        ]);
    }
}