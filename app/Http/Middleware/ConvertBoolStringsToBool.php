<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ConvertBoolStringsToBool extends TransformsRequest {
    /**
     * Конвертируем лог. значения переданные как строки в bool
     * @param string $key
     * @param mixed $value
     * @return bool|mixed
     */
    protected function transform($key, $value) {
        if ($value === 'true' || $value === 'TRUE') {
            return true;
        }
        if ($value === 'false' || $value === 'FALSE') {
            return false;
        }
        return $value;
    }
}
