<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\GroupModel;
use App\Searchers\GroupSearcher;
use App\Service\Images\ImageService;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

/**
 * Group Service. Works with a groups of products.
 * Product groups are linear list
 *
 * @package App\Service\GroupService
 */
class GroupService
{
    /**
     * Search sections by name.
     *
     * @param array $params
     *  name
     *  id
     *  onlyActive
     *  sort
     *  sortDirect
     * @return array
     * @throws \ReflectionException
     */
    public static function search(array $params = [])
    {
        $params['sort'] = $params['sort'] ?? GroupModel::SORT_ORDER;
        $params['sortDirect'] = 'asc';

        $s = new GroupSearcher();
        $groups = $s->search($params);

        // Result
        return array_map(function($item) {
            if(!empty($item['imageSrc'])) {
                $item['imageSrc'] = Utils::fullUrl($item['imageSrc']);
            }
            $item['createdAt'] = locale()->dbDtToDtStr($item['createdAt']);
            $item['updatedAt'] = locale()->dbDtToDtStr($item['updatedAt']);

            return $item;
        }, $groups);
    }


    /**
     * Get group information by id
     * @param $id
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getById($id)
    {
        $res = self::search(['id' => $id]);
        if(!empty($res)){
            return $res[0];
        }
        return null;
    }

    /**
     * Return a groups that are shown on client
     * @return array
     * @throws \ReflectionException
     */
    public static function groupsForClient() {
        // Delete all unnecessary fields
        return array_map(function($item) {
            unset($item['createdAt']);
            unset($item['deletedAt']);
            unset($item['updatedAt']);
            //unset($item['order']);
            return $item;
        }, self::search(['onlyActive' => true]));
    }

    /**
     * Update group
     *
     * @param $group
     * @param $data
     * @return GroupModel
     */
    public static function updateGroup($group, $data) {
        if(!$group instanceof GroupModel) {
            $group = GroupModel::findOrFail($group);
        }
        foreach($data as $key => $value) {
            if ('image' === $key) {
                // Replace old image
                $imgData = ImageService::getToApi(
                    GroupModel::IMAGE_GROUP_NAME,
                    $group->id
                );
                if(!empty($imgData) and !empty($imgData[0]['id'])) {
                    ImageService::delete([$imgData[0]['id']]);
                }
                ImageService::save(
                    $data['image'],
                    GroupModel::IMAGE_GROUP_NAME,
                    $group->id
                );
            } else {
                // Simple update attribute value
                $group->$key = $value;
            }
        }
        $group->updatedAt = DbHelper::currTs();
        // Save all
        $group->save();
        return $group;
    }

    /**
     * Create new group
     * @param $data
     * @return GroupModel
     */
    public static function createGroup($data) {
        $group = new GroupModel();
        $group->fill($data);
        $group->createdAt = DbHelper::currTs();
        $group->updatedAt = DbHelper::currTs();
        $group->save();

        if(!empty($data['image'])) {
            ImageService::save(
                $data['image'],
                GroupModel::IMAGE_GROUP_NAME,
                $group->id
            );
        }
        return $group;
    }
}
