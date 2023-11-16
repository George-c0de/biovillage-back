<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Locale\BaseFormatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * Базовый класс конвертации данных
 */
class BaseConverter extends BaseService {

    /**
     * Универсальная ф-я конвертации данных для фронта
     * @param $item
     * @param $settings
     */
    public static function convertToFront(&$item, $settings){
        foreach ($settings as $field => $type) {
            if(Arr::has($item, $field)){
                if($type == 'nullToEmpty'){
                    $item[$field] = $item[$field] === NULL ? '' : $item[$field];
                }
                if($type == 'storage'){
                    if(Storage::disk('public')->exists($item[$field])){
                        $item[$field] = $item[$field] ? Storage::url($item[$field]) : NULL;
                    }
                }
                if($type == 'json'){
                    if (is_array($item[$field])) {
                        $item[$field] =  Utils::objectToArray($item[$field]);
                    } else {
                        $item[$field] = Utils::objectToArray(json_decode($item[$field]));
                    }
                }
                if($type == 'array'){
                    $item[$field] = DbHelper::pgArrayToArray($item[$field]);
                }
                if(Arr::has($item, $field) && $item[$field] && explode('|', $type)[0] == 'dateTime'){
                    $item[$field] = locale()->dbDtToDtStr($item[$field]);
                }
                if(Arr::has($item, $field) && $item[$field] && explode('|', $type)[0] == 'date'){
                    $item[$field] = locale()->dbDateToDateStr($item[$field]);
                }
                if(Arr::has($item, $field) && explode('|', $type)[0] == 'trans'){
                    $item[$field] = __(explode('|', $type)[1]);
                }
            }
        }
    }

    /**
     * Универсальная ф-я конвертации данных для базы
     * @param $item
     * @param $settings
     */
    public static function convertToDb(&$item, $settings) {
        foreach ($item as $key => &$value) {
            if(is_string($value)) $value = trim($value);
        }

        foreach ($settings as $field => $type) {
            if(Arr::has($item, $field)){
                if($type == 'json'){
                    if (!is_null($item[$field])) {
                        $item[$field] = array_filter($item[$field], function($element) {
                            return !empty($element);
                        });
                        $item[$field] = json_encode($item[$field]);
                    } else {
                        $item[$field] = NULL;
                    }

                }
                if($type == 'array'){
                    if (!is_null($item[$field])) {
                        $item[$field] = array_filter($item[$field], function($element) {
                            return !empty($element);
                        });
                        $item[$field] = DbHelper::arrayToPgArray(array_values($item[$field]));
                    } else {
                        $item[$field] = NULL;
                    }
                }
            }
        }
    }

    /**
     * Конвертируем данные коллекции из базы в формат удобный для фронта
     * @param $items
     * @return array
     */
    public static function collectionToFront ($items) {
        $data = [];
        foreach ($items as $item) {
            $data[] = static::singleToFront($item);
        }

        return $data;
    }
}
