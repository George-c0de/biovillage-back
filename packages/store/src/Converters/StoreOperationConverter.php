<?php

namespace Packages\Store\Converters;

use App\Service\BaseConverter;

class StoreOperationConverter extends BaseConverter
{
    public static function singleToFront($item) {
        $item = (array) $item;

        self::convertToFront($item, [
            'storeIds' => 'array',
        ]);

        return $item;
    }
    public static function singleToDb($item) {

    }
}
