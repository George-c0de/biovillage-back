<?php namespace App\Service;

use App\Models\SettingsModel;
use App\Helpers\ResponseHelper;
use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;

class Phone
{
    private $_phone;

    public function __construct($phone)
    {
        if (!preg_match(env('PHONE_MASK'), $phone)) {
            throw new \Exception('Wrong phone format');
        }
        $this->_phone = $phone;
    }

    public function compact()
    {
        return preg_replace('~[^0-9]~', '', $this->_phone);
    }

    public function __toString()
    {
        return $this->_phone;
    }

    static public function prepare($phone)
    {
        try {
            return PhoneNumber::make($phone, env('PHONE_COUNTRY'))->formatE164();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Format phone to show client
     * @param $phone
     * @return null|string
     */
    static public function toShow($phone) {
        try {
            return PhoneNumber::make($phone, env('PHONE_COUNTRY'))->formatInternational();
        } catch (\Exception $exception) {
            return $phone;
        }
    }
}
