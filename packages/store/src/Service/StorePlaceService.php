<?php

namespace Packages\Store\Service;

use Packages\Store\Models\StorePlaceModel;

/**
 * Store Place Service. Working with operations in the store
 */
class StorePlaceService
{
    /**
     * create default places
     * @param $store
     * @return array
     */
    public static function createDefault($store){
        // system place
        $systemPlace = StorePlaceModel::create([
            'storeId' => $store['id'],
            'name' => 'credit',
            'order' => 0,
            'isSystem' => true,
        ]);

        // default place
        $defaultPlace = StorePlaceModel::create([
            'storeId' => $store['id'],
            'name' => 'Полка #1',
            'isSystem' => false,
        ]);

        return [
            'systemId' => $systemPlace['id'],
            'defaultId' => $defaultPlace['id']
        ];
    }
}
