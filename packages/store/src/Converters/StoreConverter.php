<?php

namespace Packages\Store\Converters;

use App\Service\BaseConverter;

class StoreConverter extends BaseConverter
{
    public static function singleToFront($item) {
        $item = (array) $item;

        self::convertToFront($item, [
            'createdAt' => 'dateTime',
            'updatedAt' => 'dateTime',
            'deletedAt' => 'dateTime',
        ]);

        return $item;
    }
    public static function singleToDb($item) {

    }
}
