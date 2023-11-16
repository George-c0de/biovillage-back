<?php

namespace Packages\Store\Converters;

use App\Service\BaseConverter;
use Illuminate\Support\Arr;

class StorePlaceConverter extends BaseConverter
{
    public static function singleToFront($item) {
        $item = (array) $item;

        self::convertToFront($item, [
            'createdAt' => 'dateTime',
            'updatedAt' => 'dateTime',
            'deletedAt' => 'dateTime',
        ]);

        if(Arr::has($item, 'storePlaceName') && $item['storePlaceName'] == 'credit'){
            $item['storePlaceName'] = trans('store.'.$item['storePlaceName']);
        }

        if(Arr::has($item, 'name') && $item['name'] == 'credit'){
            $item['name'] = trans('store.'.$item['name']);
        }

        return $item;
    }
    public static function singleToDb($item) {

    }
}
