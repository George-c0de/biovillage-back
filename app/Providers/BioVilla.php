<?php

namespace App\Providers;

use App\Service\BillingService;
use App\Service\BonusesService;
use App\Service\GiftService;
use App\Service\PackageService;
use App\Service\PaymentHandlerService;
use App\Service\OrderHandlerService;
use App\Service\ReferralService;
use Illuminate\Support\ServiceProvider;

class BioVilla extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('Gifts', GiftService::class);
        app()->singleton('Billing', BillingService::class);
        app()->singleton('Bonuses', BonusesService::class);
        app()->singleton('OrderStatusHandler', OrderHandlerService::class);
        app()->singleton('Referral', ReferralService::class);
        app()->singleton('PaymentStatusHandler', PaymentHandlerService::class);

        // Подключение сервисов всех пакетов, если пакет не подключен в composer.json то resolve сработает в холостую
        foreach (PackageService::SETTINGS as $provider => $data) {
            app()->singleton($data['name'], function ($app) use ($provider) {
                return PackageService::getSingletonClass($provider);
            });
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
