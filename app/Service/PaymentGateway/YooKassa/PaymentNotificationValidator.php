<?php

namespace App\Service\PaymentGateway\YooKassa;

use App\Helpers\DbHelper;
use App\Models\Auth\Client;
use App\Models\OrderModel;
use App\Models\SettingsModel;
use App\Service\PaymentGateway\IPaymentNotificationContext;
use App\Service\PaymentGateway\IPaymentNotificationValidator;
use App\Service\SettingsService;
use Illuminate\Validation\Rule;
use YooKassa\Model\NotificationEventType as NET;
use Wikimedia\IPSet;
use Illuminate\Support\Facades\App;

class PaymentNotificationValidator implements IPaymentNotificationValidator
{


    /**
     * Notification rules
     * @return array
     */
    public function rules(): array
    {
        return [
            'event' => [
                'required',
                Rule::in([
                    NET::PAYMENT_WAITING_FOR_CAPTURE,
                    NET::PAYMENT_CANCELED,
                    NET::PAYMENT_SUCCEEDED,
                    NET::REFUND_SUCCEEDED
                ])
            ],
            'object' => 'required', // important!!!
            'object.id' => 'required',
            'object.status' => 'required',
            'object.paid' => sprintf(
                'required_unless:event,%s|boolean',
                NET::REFUND_SUCCEEDED
            ),
            'object.amount.value' => 'required|numeric',
            'object.amount.currency' => sprintf('
                required|in:%s',
                SettingsService::getSettingValue(
                    SettingsModel::SETTING_PAYMENT_CURRENCY_CODE)
            ),
            'object.metadata.orderId' => sprintf(
                'required_unless:event,%s|integer|exists:%s,id|min:1|max:%d',
                NET::REFUND_SUCCEEDED, with(new OrderModel())->getTable(), DbHelper::MAX_INT
            ),
            'object.metadata.token' => sprintf(
                'required_unless:event,%s|in:%s',
                NET::REFUND_SUCCEEDED, env('PAYMENT_GATEWAY_SECURE_TOKEN', '')
            )
        ];
    }

    /**
     * Addition validation logic
     * @param $data
     * @param $errorsBag
     * @return mixed
     */
    public function validate($data, $errorsBag) {

        // YooKassa IPs
        $ipSet = new IPSet([
            '185.71.76.0/27',
            '185.71.77.0/27',
            '77.75.153.0/25',
            '77.75.154.128/25',
            '2a02:5180:0:1509::/64',
            '2a02:5180:0:2655::/64',
            '2a02:5180:0:1533::/64',
            '2a02:5180:0:2669::/64'
        ]);
        if(App::environment('production') && !$ipSet->match($data['ip'] ?? '')) {
            $errorsBag->add('ip', trans('validator.wrongIP'));
        }
    }

    /**
     * Make notification context
     * @param $data
     * @return mixed
     */
    public function context($data): IPaymentNotificationContext
    {
        return new PaymentNotificationContext($data);
    }
}