<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;


class BaseFrontController extends BaseController {

    public function noop() {
        return 'noop';
    }
}