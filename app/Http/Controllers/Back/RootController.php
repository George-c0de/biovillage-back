<?php

namespace App\Http\Controllers\Back;

use App\Service\SettingsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Back\BaseBackController;

class RootController extends BaseBackController
{
    /**
     * Показываем основную страницу админки
     */
    public function index() {
        return view('back.index', [
            'settings' => SettingsService::getSettings()
        ]);
    }
}
