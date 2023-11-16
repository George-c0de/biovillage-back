<?php namespace App\Http\Requests\Settings;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\PaymentModel;
use App\Models\SettingsModel as SM;
use App\Providers\LocaleServiceProvider;
use App\Service\BillingService;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Attribute must be same a SettingsModel consts
     * @return array
     */
    public function rules()
    {
        return [
            // System
            SM::SETTING_CASHBACK_ITEM => 'integer|min:0|max:10000',
            SM::SETTING_CASHBACK_EACH_ORDER => 'integer|min:0|max:10000',
            SM::SETTING_CASHBACK_FIRST_ORDER => 'integer|min:0|max:10000',
            SM::SETTING_REFERAL_BONUS => 'integer|min:0|max:10000',
            SM::SETTING_MAX_REFERALS => 'integer|min:0|max:1000',
            SM::SETTING_MOBILE_PHONE_FORMAT => 'string|max:64',
            SM::SETTING_LANGUAGE => [
                'string',
                Rule::in(LocaleServiceProvider::SUPPORTED_LANGUAGES)
            ],

            // Delivery
            SM::SETTING_DELIVERY_FREE_SUM => 'int|min:0|max:'.DbHelper::MAX_INT,
            SM::SETTING_DELIVERY_DESC => 'string|min:1|max:5000',
            SM::SETTING_DELIVERY_TODAY_HOUR => 'int|min:0|max:24',
            SM::SETTING_DELIVERY_TITLE => 'string|max:255',
            SM::SETTING_MAX_DELIVERY_IN_INTERVAL => 'int|max:10000',

            // Payment
            SM::SETTING_PAYMENT_DESC => 'string|min:1|max:5000',
            SM::SETTING_PAYMENT_CASH => 'boolean',
            SM::SETTING_PAYMENT_CCARD => 'boolean',
            SM::SETTING_PAYMENT_APAY => 'boolean',
            SM::SETTING_PAYMENT_GPAY => 'boolean',
            SM::SETTING_PAYMENT_BONUS => 'boolean',
            SM::SETTING_PAYMENT_BONUS => 'boolean',
            SM::SETTING_PAYMENT_CARD => 'boolean',
            SM::SETTING_PAYMENT_SHOP_ID => 'string|max:255',
            SM::SETTING_PAYMENT_RETURN_URL => 'string|max:255',
            SM::SETTING_PAYMENT_MOBILE_KEY => 'string|max:255',
            SM::SETTING_PAYMENT_SECRET_KEY => 'string|max:255',
            SM::SETTING_PAYMENT_PURCHASE_DESC => 'string|max:1024',
            SM::SETTING_PAYMENT_PURCHASE_NAME => 'string|max:255',
            SM::SETTING_PAYMENT_CURRENCY_CODE => 'string|max:3',
            SM::SETTING_PAYMENT_CURRENCY_SIGN => 'string|max:3',
            SM::SETTING_PAYMENT_GATEWAY => [ Rule::in([
                BillingService::GATEWAY_YOOKASSA,
                BillingService::GATEWAY_STRIPE
            ])],

            // Office
            SM::SETTING_OFFICE_ADDRESS => 'string|min:1|max:5000',
            SM::SETTING_OFFICE_LAT => [
                'string',
                'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'
            ],
            SM::SETTING_OFFICE_LON => [
                'string',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'
            ],
            SM::SETTING_OFFICE_PHONE_CLIENT => 'string|min:1|max:5000',
            SM::SETTING_OFFICE_PHONE_PARTNERS => 'string|min:1|max:5000',
            SM::SETTING_OFFICE_EMAIL => 'string|min:1|max:5000|email',

            // Socials
            SM::SETTING_SOCIAL_VK => 'string|min:1|max:5000',
            SM::SETTING_SOCIAL_INSTA => 'string|min:1|max:5000',
            SM::SETTING_SOCIAL_FB  => 'string|min:1|max:5000',
            SM::SETTING_SOCIAL_TG  => 'string|min:1|max:5000',

            // Telegram
            SM::SETTING_SUPPORT_TG => 'string|min:1|max:5000',

            // About
            SM::SETTING_ABOUT_PURPOSE => 'string|min:1|max:5000',
            SM::SETTING_ABOUT_ADVANTAGE1 => 'string|min:1|max:5000',
            SM::SETTING_ABOUT_ADVANTAGE2 => 'string|min:1|max:5000',
            SM::SETTING_ABOUT_ADVANTAGE3 => 'string|min:1|max:5000',
            SM::SETTING_ABOUT_ADVANTAGE4 => 'string|min:1|max:5000',
            SM::SETTING_ABOUT_ORG_DETAILS => 'string|min:1|max:5000',

            // Director
            SM::SETTING_FOUNDER_NAME => 'string|min:1|max:5000',
            SM::SETTING_FOUNDER_PHOTO => 'image|mimes:jpeg,png,jpg|max:5000',

            // Maps
            SM::SETTING_MAP_TOKEN => 'string|min:1|max:128',
            SM::SETTING_MAP_PRIMARY_CITY => 'string|min:1|max:128',
            SM::SETTING_MAP_CENTER_LAT => [
                'string',
                'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'
            ],
            SM::SETTING_MAP_CENTER_LON => [
                'string',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'
            ],
            SM::SETTING_MAP_ZOOM => 'regex:/^\d+[\.,]?\d?/',
            SM::SETTING_MAP_SEARCH_RADIUS => 'int|min:1|max:500000',

            // Promo products
            SM::SETTING_PROMO_PRODS_NAME => 'string|max:255',
            SM::SETTING_PROMO_PRODS_IMG => 'image|mimes:jpeg,png,jpg|max:5000',
            SM::SETTING_PROMO_PRODS_BG_COLOR => 'string|min:1|max:10|regex:/^#\w{3,10}$/',

            SM::SETTING_STORE_CAN_ADMIN_MINUS => 'boolean',
        ];
    }
}
