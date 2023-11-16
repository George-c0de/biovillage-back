<?php namespace App\Service\Images;

use App\Helpers\Utils;
use App\Models\ImageModel;
use Illuminate\Support\Arr;
use App\Models\ProductModel;
use App\Service\StorageService;
use App\Service\CustomModelService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\FileBag;

/**
 * Class ImageService
 * @package App\Service
 */
class ImageService {
    /**
     * Сохранение картинки\картинок
     *
     * @param $files
     * @param $groupName
     * @param $entityId
     * @param $thumbSize
     * @param $folder
     *
     * @return array|null
     */
    public static function save($files, $groupName, $entityId, $thumbSize = false, $folder = 'images') {

        $data = [];

        $lastOrder = self::getLastOrder($groupName, $entityId);
        $order = $lastOrder + 1;

        if (is_array($files)) {
            foreach ($files as $file) {
                $imgPath = StorageService::saveMd5($file, $folder);
                $data[] = self::fillData($imgPath, $groupName, $entityId, $order, $thumbSize);
                $order += 1;
            }
        } else { // Если в поле одна картинка
            $imgPath = StorageService::saveMd5($files, $folder);
            $data = self::fillData($imgPath, $groupName, $entityId, $order, $thumbSize);
        }

        if ($data) ImageModel::insert($data);

        return $data ?? null;
    }

    /**
     * Удаление картинки\картинок по ids
     * @param $ids
     */
    public static function delete($ids) {
        $imageModel = ImageModel::whereIn('id', $ids);
        $imageModel->delete();
    }

    /**
     * Удаление картинок в сущностях по entityIds
     * @param $groupName
     * @param $entityId
     */
    public static function deleteByEntities($groupName, $entityId) {

        if(!is_array($entityId)) {
            $entityId = [$entityId];
        }
        foreach ($entityId as $eid) {
            $imageIds = Collection::make(
                ImageService::getToApi($groupName, $entityId)
            )->pluck('id')->toArray();
            if(!empty($imageIds)) {
                ImageService::delete($imageIds);
            }
        }
    }


    /**
     * Обновление картинки\картинок
     * Обновляются только поля не содержащие пути к картинкам
     * например order
     * Принимаем многомерный ассоциативный массив ключом является id записи в значении массив с полями которые необходимо обновить
     * в данном случае order
     *
     * Почему так?
     * потому что изменения по сути нет есть удаление картинок и добавление новых этого достаточно
     * изменение нужны только для полей не связанных с src и srcThumb
     * @param $settings
     * @param $data
     * @throws \Exception
     */
    public static function update($data) {
        $imageCustomModel = new CustomModelService('images');
        $settings = [
            ['id' => 'type:integer'],
            ['order' => 'type:integer']
        ];
        $imageCustomModel->updateMultiple($settings, $data);
    }

    /**
     * Ф-я отдает массив для сохранения в базу
     * @param $imgPath
     * @param $groupName
     * @param $entityId
     * @param $order
     * @param $thumbSize
     * @return array
     */
    public static function fillData($imgPath, $groupName, $entityId, $order, $thumbSize = false) {
        $data = [
            'entityId' => $entityId,
            'groupName' => $groupName,
            'src' => $imgPath,
            'order' => $order,
        ];

        if ($thumbSize) {
            $imgThumbPath = StorageService::thumbnail($imgPath, $thumbSize);
            $data['srcThumb'] = $imgThumbPath;
        }

        return $data;
    }

    /**
     * Получить данные удобные для api
     * @param $groupName
     * @param $entityId
     * @return array
     */
    public static function getToApi($groupName, $entityId) {

        $images = ImageModel::select(
           [
               'id',
               'src',
               'srcThumb',
               'order',
           ]
        )->where([
            ['groupName', $groupName],
            ['entityId', $entityId],
        ])->whereNull(
            'deletedAt'
        )->orderBy('order', 'asc')->get();

        if (!$images) {
            return [];
        }

        $images = $images->toArray();
        foreach ($images as &$image) {
            $image['src'] = Storage::disk('public')->exists( $image['src'] ) ? Storage::url($image['src']) : $image['src'];
            $image['srcThumb'] = Storage::disk('public')->exists($image['srcThumb']) ? Storage::url($image['srcThumb']) : $image['srcThumb'];
        }

        return $images;
    }

    public static function getToApiFullUrl($groupName, $entityId) {
        return array_map(function($img) {
            $img['src'] = Utils::fullUrl($img['src']);
            $img['srcThumb'] = Utils::fullUrl($img['srcThumb']);
            return $img;
        }, self::getToApi($groupName, $entityId));
    }

    /**
     * Отдаем последний порядок сортировки
     * @param $groupName
     * @param $entityId
     * @return int
     */
    public static function getLastOrder($groupName, $entityId) {
        $lastOrder = 0;
        $lastImages = ImageModel::where([
            ['groupName', $groupName],
            ['entityId', $entityId]
        ])->orderBy('order', 'desc')->first();

        if ($lastImages) {
            $lastOrder = $lastImages->order;
        }

        return $lastOrder;
    }
}
