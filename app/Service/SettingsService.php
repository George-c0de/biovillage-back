<?php

namespace App\Service;

use App\Helpers\Utils;
use App\Models\CatalogSection;
use App\Models\SettingsModel;
use App\Models\TagModel;
use App\Service\Images\ImageService;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 *
 * Work with product tags
 *
 * @package App\Service\GroupService
 */
class SettingsService extends BaseService
{

    private static $settings;

    /**
     * Common settings
     * @param bool $fresh
     * @return array
     */
    public static function getSettings($fresh = false) {

        if(empty(static::$settings) || $fresh) {
            static::$settings = array_intersect_key(
                array_merge(
                    SettingsModel::DEFAULT_SETTINGS,
                    self::getRawSettings()
                ),
                SettingsModel::DEFAULT_SETTINGS
            );

            foreach(SettingsModel::SETTINGS_TO_FLOAT as $ff) {
                static::$settings[$ff] = floatval(
                    static::$settings[$ff] ?? 0);
            }
            // 0 or 1 as boolean
            foreach(SettingsModel::SETTINGS_TO_BOOLEAN as $ff) {
                static::$settings[$ff] = intval(
                    static::$settings[$ff] ?? 0);
            }
            foreach (SettingsModel::SETTING_TO_INT as $ff) {
                static::$settings[$ff] = intval(
                    static::$settings[$ff] ?? 0);
            }
        }

        return static::$settings;
    }

    /**
     * Get only saved settings
     */
    public static function getRawSettings() {
        return SettingsModel::all()->pluck(
            'value', 'key')->toArray();
    }

    /**
     * Return one setting value
     * @param $key
     * @return array|string
     */
    public static function getSettingValue($key = null) {
        if(is_null($key)) {
            return self::getSettings();
        }
        $settings = self::getSettings();
        return $settings[$key] ?? Utils::raise('No setting ' . $key);
    }

    /**
     * Update settings
     * @param array $data
     *  key => value
     */
    public static function updateSettings($data = []) {


        // Save images first
        // $i as entityId
        foreach( SettingsModel::SETTING_TO_UPLOAD as $i => $k) {
            if (empty($data[$k]) ) {
                continue;
            }
            ImageService::deleteByEntities(
                SettingsModel::SETTINGS_IMAGE_GROUP, $i
            );
            $img = ImageService::save(
                $data[$k],
                SettingsModel::SETTINGS_IMAGE_GROUP,
                $i
            );
            if($img['src']) {
                $data[$k] = Utils::fullUrl($img['src']);
            }
        }

        // Other settings
        $settings = self::getRawSettings();

        // To update
        $toUpdate = array_intersect(array_keys($settings), array_keys($data));
        foreach($toUpdate as $key) {
            SettingsModel::where('key', $key)
                ->update(['value' => $data[$key]]);
        }

        // To insert
        $toInsert = array_diff(array_keys($data), array_keys($settings));
        foreach($toInsert as $key) {
            SettingsModel::create([
                'key' => $key, 'value' => $data[$key]
            ])->save();
        }
    }
    /**
     * Settings for mobile clients
     */
    public static function getSettingsForClient() {

        return array_merge(
            // General settings
            self::getSettings(),
            [
                'tags' => TagService::getClientTags(),
                'groups' => GroupService::groupsForClient(),
                'mainSlider' => SliderService::getClientSliders(),
                'di' => DeliveryIntervalService::getActiveIntervals(),
                'da' => DeliveryAreaService::getAreasPolygonParsed(),
            ]
        );
    }

}
