<?php

namespace App\Service;

use Illuminate\Support\Arr;
use Packages\Purchaser\PurchaserServiceProvider;
use Packages\Store\Service\StoreResolveService;
use Packages\Store\StoreServiceProvider;

class PackageService
{
    // Package names
    const STORE_PACKAGE = 'Store';
    const PURCHASER = 'Purchaser';

    // Подключение пакетов
    // Основной сервис провайдер пакета
    // name => алиас (имя) пакета
    // service => основной сервис пакета
    const SETTINGS = [
        StoreServiceProvider::class => [
            'name' => self::STORE_PACKAGE,
            'service' => StoreResolveService::class,
        ],
        PurchaserServiceProvider::class => [
            'name' => self::PURCHASER,
        ],
    ];

    /**
     * Get all loaded packages from project
     * @return array
     */
    public static function getPackages () {
        $providers = app()->getLoadedProviders();
        $packages = null;

        foreach ($providers as $provider => $bool) {
            if($bool && Arr::has(PackageService::SETTINGS, $provider)) {
                $packages[] = PackageService::SETTINGS[$provider]['name'];
            };
        }

        return $packages;
    }

    public static function hasPackage($name) {
        $packages = self::getPackages();
        return in_array($name, $packages);
    }

    /**
     * Get all loaded packages from project
     * @return array
     */
    public static function getPackageProviders () {
        $providers = app()->getLoadedProviders();
        $packageProviders = null;

        foreach ($providers as $provider => $bool) {
            if($bool && Arr::has(PackageService::SETTINGS, $provider)) {
                $packageProviders[] = $provider;
            };
        }

        return $packageProviders;
    }

    /**
     * Determines if a specific package is connected to the project
     * @param $provider
     * @return bool
     */
    public static function hasPackageByProvider($provider) {
        $packageProviders = self::getPackageProviders();
        return in_array($provider, $packageProviders);
    }

    /**
     * @param $provider
     * @return EmptyClassService or ServiceClass
     */
    public static function getSingletonClass($provider) {
        $providerSetting = self::SETTINGS[$provider];

        if(Arr::has($providerSetting, 'service')) {
            $service = $providerSetting['service'];
            if(self::hasPackageByProvider($provider) && class_exists($service)) {
                return new $service();
            }
        }

        return new EmptyClassService();
    }
}
