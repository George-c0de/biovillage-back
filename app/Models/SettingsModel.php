<?php namespace App\Models;

use App\Service\BillingService;
use Illuminate\Database\Eloquent\Model;

class SettingsModel extends Model
{

    // System
    const SETTING_CASHBACK_ITEM = 'cashbackForNewItems';
    const SETTING_CASHBACK_FIRST_ORDER = 'cashbackPercentForFirstOrder';
    const SETTING_CASHBACK_EACH_ORDER = 'cashbackPercentForEachOrder';
    const SETTING_REFERAL_BONUS = 'bonusesForReferral';
    const SETTING_MAX_REFERALS = 'maxReferralsPerUser';
    const SETTING_MOBILE_PHONE_FORMAT = 'mobilePhoneFormat';
    const SETTING_LANGUAGE = 'lang';

    // Delivery
    const SETTING_DELIVERY_DESC = 'deliveryDesc';
    const SETTING_DELIVERY_FREE_SUM = 'deliveryFreeSum';
    const SETTING_DELIVERY_TODAY_HOUR = 'deliveryTodayHour';
    const SETTING_DELIVERY_TITLE = 'deliveryTitle';
    const SETTING_MAX_DELIVERY_IN_INTERVAL = 'deliveryMaxInInterval';

    // Payment
    const SETTING_PAYMENT_DESC = 'paymentDesc';
    const SETTING_PAYMENT_CASH = 'paymentCashEnabled';
    const SETTING_PAYMENT_CCARD = 'paymentCCardEnabled';
    const SETTING_PAYMENT_GPAY = 'paymentGPayEnabled';
    const SETTING_PAYMENT_APAY = 'paymentAPayEnabled';
    const SETTING_PAYMENT_CARD = 'paymentCardEnabled';
    const SETTING_PAYMENT_BONUS = 'paymentBonusEnabled';
    const SETTING_PAYMENT_GATEWAY = 'paymentGateway';
    const SETTING_PAYMENT_CURRENCY_CODE = 'paymentCurrency';
    const SETTING_PAYMENT_CURRENCY_SIGN = 'paymentCurrencySign';

    const SETTING_PAYMENT_SHOP_ID = 'paymentGatewayShopId';
    const SETTING_PAYMENT_MOBILE_KEY  = 'paymentGatewayMobileKey';
    const SETTING_PAYMENT_SECRET_KEY  = 'paymentGatewaySecretKey';
    const SETTING_PAYMENT_RETURN_URL = 'paymentGatewayReturnUrl';
    const SETTING_PAYMENT_PURCHASE_NAME ='paymentGatewayPurchaseName';
    const SETTING_PAYMENT_PURCHASE_DESC = 'paymentGatewayPurchaseDesc';

    // Office
    const SETTING_OFFICE_ADDRESS = 'officeAddress';
    const SETTING_OFFICE_LAT = 'officeLat';
    const SETTING_OFFICE_LON = 'officeLon';
    const SETTING_OFFICE_PHONE_CLIENT = 'officePhoneClient';
    const SETTING_OFFICE_PHONE_PARTNERS = 'officePhonePartners';
    const SETTING_OFFICE_EMAIL = 'officeEmail';

    // Social
    const SETTING_SOCIAL_VK = 'socialVk';
    const SETTING_SOCIAL_INSTA = 'socialInstagram';
    const SETTING_SOCIAL_FB = 'socialFacebook';
    const SETTING_SOCIAL_TG = 'socialTelegram';

    // Support
    const SETTING_SUPPORT_TG = 'supportTelegram';

    // About
    const SETTING_ABOUT_PURPOSE = 'aboutPurposes';
    const SETTING_ABOUT_ADVANTAGE1 = 'aboutAdvantage1';
    const SETTING_ABOUT_ADVANTAGE2 = 'aboutAdvantage2';
    const SETTING_ABOUT_ADVANTAGE3 = 'aboutAdvantage3';
    const SETTING_ABOUT_ADVANTAGE4 = 'aboutAdvantage4';
    const SETTING_ABOUT_ORG_DETAILS = 'aboutOrgDetails';

    // Founder
    const SETTING_FOUNDER_NAME = 'founderName';
    const SETTING_FOUNDER_PHOTO = 'founderPhoto';

    // Promo products
    const SETTING_PROMO_PRODS_NAME = 'promoProdsName';
    const SETTING_PROMO_PRODS_BG_COLOR = 'promoProdsBgColor';
    const SETTING_PROMO_PRODS_IMG = 'promoProdsImg';

    // Cities
    const SETTING_MAP_TOKEN = 'mapsToken';
    const SETTING_MAP_PRIMARY_CITY = 'mapsPrimaryCity';
    const SETTING_MAP_CENTER_LAT = 'mapsCenterLat';
    const SETTING_MAP_CENTER_LON = 'mapsCenterLon';
    const SETTING_MAP_ZOOM = 'mapsZoom';
    const SETTING_MAP_SEARCH_RADIUS = 'mapSearchRadius';


    // Settings image group
    const SETTINGS_IMAGE_GROUP = 'settings';

    const SETTING_STORE_CAN_ADMIN_MINUS = 'storeCanAdminMinus';

    // Do upload file these settings. Order is important.
    const SETTING_TO_UPLOAD = [
        self::SETTING_FOUNDER_PHOTO,
        self::SETTING_PROMO_PRODS_IMG
    ];

    // Int settings
    const SETTING_TO_INT = [
        self::SETTING_CASHBACK_ITEM,
        self::SETTING_REFERAL_BONUS,
        self::SETTING_MAX_REFERALS,
        self::SETTING_DELIVERY_FREE_SUM,
        self::SETTING_DELIVERY_TODAY_HOUR,
        self::SETTING_MAX_DELIVERY_IN_INTERVAL,
        self::SETTING_CASHBACK_EACH_ORDER,
        self::SETTING_CASHBACK_FIRST_ORDER,
        self::SETTING_MAP_SEARCH_RADIUS
    ];

    // Float settings
    const SETTINGS_TO_FLOAT = [
        self::SETTING_OFFICE_LON,
        self::SETTING_OFFICE_LAT,
        self::SETTING_MAP_CENTER_LAT,
        self::SETTING_MAP_CENTER_LON,
        self::SETTING_MAP_ZOOM,
    ];

    const SETTINGS_TO_BOOLEAN = [
        self::SETTING_PAYMENT_GPAY,
        self::SETTING_PAYMENT_APAY,
        self::SETTING_PAYMENT_CASH,
        self::SETTING_PAYMENT_CCARD,
        self::SETTING_PAYMENT_BONUS,
        self::SETTING_PAYMENT_CARD,
        self::SETTING_STORE_CAN_ADMIN_MINUS,
    ];

    // Default settings
    const DEFAULT_SETTINGS = [
        self::SETTING_CASHBACK_EACH_ORDER => 10,
        self::SETTING_CASHBACK_FIRST_ORDER => 20,
        self::SETTING_CASHBACK_ITEM => 5,
        self::SETTING_REFERAL_BONUS => 100,
        self::SETTING_MAX_REFERALS => 10,
        self::SETTING_MOBILE_PHONE_FORMAT => '+7 000 000-00-00',
        self::SETTING_LANGUAGE => 'en',

        self::SETTING_DELIVERY_DESC => 'Delivery description here',
        self::SETTING_DELIVERY_FREE_SUM => 100000,
        self::SETTING_DELIVERY_TODAY_HOUR => 12,
        self::SETTING_DELIVERY_TITLE => 'Delivery',
        self::SETTING_MAX_DELIVERY_IN_INTERVAL => 100,

        self::SETTING_PAYMENT_DESC => 'Payment description here',
        self::SETTING_PAYMENT_CASH => 1,
        self::SETTING_PAYMENT_CCARD => 1,
        self::SETTING_PAYMENT_GPAY => 0,
        self::SETTING_PAYMENT_APAY => 0,
        self::SETTING_PAYMENT_BONUS => 0,
        self::SETTING_PAYMENT_CARD => 1,
        self::SETTING_PAYMENT_GATEWAY => BillingService::GATEWAY_YOOKASSA,
        self::SETTING_PAYMENT_CURRENCY_CODE => 'RUB',
        self::SETTING_PAYMENT_CURRENCY_SIGN => '₽',
        self::SETTING_PAYMENT_SHOP_ID => '',
        self::SETTING_PAYMENT_MOBILE_KEY => '',
        self::SETTING_PAYMENT_SECRET_KEY => '',
        self::SETTING_PAYMENT_RETURN_URL => '',
        self::SETTING_PAYMENT_PURCHASE_NAME => '',
        self::SETTING_PAYMENT_PURCHASE_DESC => '',

        self::SETTING_OFFICE_ADDRESS => '1250 Baltimore Pike, Springfield Mall, PA 19064',
        self::SETTING_OFFICE_LAT => ' 	39.91518385',
        self::SETTING_OFFICE_LON => '-75.35176834741706',
        self::SETTING_OFFICE_PHONE_CLIENT => '+1 (610) 328-1200 ',
        self::SETTING_OFFICE_PHONE_PARTNERS => '+1 (610) 328-1200 ',
        self::SETTING_OFFICE_EMAIL => 'info@shopspringfieldmall.com',

        self::SETTING_SOCIAL_VK => '',
        self::SETTING_SOCIAL_INSTA => '',
        self::SETTING_SOCIAL_FB => 'https://www.facebook.com/Springfieldmall',
        self::SETTING_SOCIAL_TG => '@Ob_nal',

        self::SETTING_SUPPORT_TG => '',

        self::SETTING_ABOUT_PURPOSE => 'In this chapter, you learn the traits of successful project managers, the reasons that project managers succeed, and the reasons that they fail.',
        self::SETTING_ABOUT_ADVANTAGE1 => 'Several of your project team members are pulled off the project to work for someone else in your organization. You will make do.',
        self::SETTING_ABOUT_ADVANTAGE2 => 'You learn that an essential piece of equipment that was promised to you is two weeks late. You will improvise.',
        self::SETTING_ABOUT_ADVANTAGE3 => 'You discover that key assumptions you made during the early implementation phases turned out to be wildly off the mark. You will adjust.',
        self::SETTING_ABOUT_ADVANTAGE4 => 'One-third of the way into the project a minicrisis develops in your domestic life. You will prevail regardless',
        self::SETTING_ABOUT_ORG_DETAILS => "ООО \"БИОВИЛЛАДЖ\"\nРуководитель: Аргашоков Руслан Юрьевич\nИНН: 7734438979\nОГРН: 1207700459339\nКПП: 773401001\nРасчётный счёт: 40702810138000075506\nБанк: ПАО СБЕРБАНК\nБИК: 044525225\nКор. счёт: 30101810400000000225\n",

        self::SETTING_FOUNDER_NAME => 'Ivan Shamal',
        self::SETTING_FOUNDER_PHOTO => 'https://placekitten.com/g/200/300',

        self::SETTING_MAP_TOKEN => '<token here>',
        self::SETTING_MAP_PRIMARY_CITY => 'New-York',
        self::SETTING_MAP_CENTER_LAT => 55.75,
        self::SETTING_MAP_CENTER_LON => 37.6167,
        self::SETTING_MAP_ZOOM => 1.6,
        self::SETTING_MAP_SEARCH_RADIUS => 50,

        self::SETTING_PROMO_PRODS_NAME => 'Promo products',
        self::SETTING_PROMO_PRODS_BG_COLOR => '#B3B3B3',
        self::SETTING_PROMO_PRODS_IMG => 'https://dummyimage.com/200x200',

        self::SETTING_STORE_CAN_ADMIN_MINUS => true,
    ];

    /**
     * There is no primary key (ID)
     */
    public $primaryKey = null;
    public $incrementing = false;

    /**
     * There is no timestamps (created_at, updated_at)
     */
    public $timestamps = false;

    protected $table = 'settings';

    protected $fillable = [ 'key', 'value' ];

}
