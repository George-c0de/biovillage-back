<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

class BaseApiController extends BaseController {

    public function noop() {
        return [get_class($this) => true];
    }
}