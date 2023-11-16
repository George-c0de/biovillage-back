<?php

namespace App\Service;

use App\Helpers\Notify;
use App\Models\PaymentModel;
use App\Models\SettingsModel;

class PaymentHandlerService extends BaseService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = SettingsService::getSettings();
    }

    /**
     * @param $payment
     */
    public function cancelPayment(PaymentModel $payment) {
        Notify::sendSms(
            $payment->order->client()->phone,
            'sms.payment.cancel',
            [
                'phoneSupport' => $this->settings[SettingsModel::SETTING_OFFICE_PHONE_CLIENT],
                'emailSupport' => $this->settings[SettingsModel::SETTING_OFFICE_EMAIL],
            ]
        );
    }
}