<?php

namespace App\Service\Catalog;

use App\Helpers\DbHelper;
use App\Service\BaseConverter;
use Illuminate\Support\Arr;

/**
 * Класс конвертации данных каталога
 */
class CatalogConverter extends BaseConverter {

    /**
     * Конвертация записей полученных из бд в многомерный массив
     * @param $catalog
     * @return array
     */
    public static function toApi($catalog)
    {
        $helpCatalog = [];
        $multiCatalog = [];

        foreach ($catalog as $item) {
            $item = self::singleToFront($item);
            Arr::set($helpCatalog, $item['id'], $item);
        }

        foreach ($helpCatalog as $item) {
            $helpPath = '';
            $pathId = $item['pathId'];

            if ($item['isGroup']) {
                $item = array_merge($item, [
                    'groups' => [],
                    'products' => [],
                ]);
            }

            // Если элемент корневой
            if (
                !$helpPath &&
                !$item['parentId'] &&
                count($pathId) == 1
            ) {
                $helpPath .= $item['id'];
                // Если элемент вложенный
            } else {
                foreach ($pathId as $key => $pathIdItem) {
                    // Если в $helpCatalog нету id из массива пути $path то значит этот элемент удален пропускаем итерацию
                    if (!Arr::has($helpCatalog, $pathIdItem)) {
                        continue 2;
                    }

                    $isGroup = $helpCatalog[$pathIdItem]['isGroup'];
                    $groupKey= $isGroup ? 'groups' : 'products';

                    $helpPath .= $key == 0 ? $pathIdItem : '.'.$groupKey.'.'.$pathIdItem;
                }
            }

            Arr::set($multiCatalog, $helpPath, $item);
        }

        return [
            'multiCatalog' => self::normalizeCatalog($multiCatalog),
            'helpCatalog' => $helpCatalog,
        ];
    }

    /**
     * Преобразуем ассоциативный массив groups с ключами id
     * в каждом элементе каталога в обычный нумерованный массив
     * @param $catalog
     * @return array
     */
    public static function normalizeCatalog(&$catalog) {
        $catalog = array_values($catalog);
        foreach ($catalog as $key => &$item) {

            if(Arr::has($item, 'products')){
                $item['products'] = array_values($item['products']);
            }
            if(Arr::has($item, 'groups')){
                $item['groups'] = self::normalizeCatalog($item['groups']);
            }
        }
        return $catalog;
    }

    /**
     * Конвертация значений для одного товара каталога
     * @param $item
     * @return array
     */
    public static function singleToFront($item) {
        $item = (array) $item;

        self::convertToFront($item, [
            'pathId' => 'array',
            'images' => 'array',
        ]);

        return $item;
    }
}
