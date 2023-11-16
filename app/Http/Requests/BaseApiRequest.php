<?php
namespace App\Http\Requests;

class BaseApiRequest extends BaseRequest {
    // Need json
    protected $jsonError = true;
}
