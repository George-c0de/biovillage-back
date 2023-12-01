<?php

namespace App\Service;

use App\Helpers\ResponseHelper;
use Propaganistas\LaravelPhone\PhoneNumber;

class PhoneService
{

    const DEBUG_PHONE_RE = '/7999999999\d/';

    //
    static public function compact($phone)
    {
        return preg_replace('~[^0-9]~', '', $phone);
    }

    //
    static public function format($phone)
    {
        return '+' . substr($phone, 0, 1) . ' (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9, 2);
    }

    //
    public static function preparePhoneNumber($phone)
    {
        try {
            $phone = PhoneNumber::make($phone, env('PHONE_COUNTRY', 'RU'))->formatE164();
        } catch (\Exception $exception) {
            return ResponseHelper::errorKey('phone', trans('errors.wrongPhoneNumber'));
        }

        return $phone;
    }

    public static function buildPhoneWithPrefix($phone)
    {
        $time = time();
        return "{$time}_{$phone}";
    }


    /**
     * May be it is test phone
     * @param $phone
     * @return bool
     */
    public static function isTestPhone($phone)
    {

        return preg_match(self::DEBUG_PHONE_RE, static::compact($phone));
    }
}
