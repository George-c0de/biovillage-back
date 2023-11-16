<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;

class BaseBackController extends BaseController {


    public function noop() {
        return 'noop';
    }
}