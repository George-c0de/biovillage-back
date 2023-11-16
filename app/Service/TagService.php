<?php

namespace App\Service;

use App\Models\CatalogSection;
use App\Models\TagModel;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

/**
 *
 * Work with product tags
 *
 * @package App\Service\GroupService
 */
class TagService extends BaseService
{

    /**
     * Return tags list for show clients
     */
    public static function getClientTags() {
        return TagModel::select('id', 'name')
            ->where('active', '=', true)
            ->whereNull('deletedAt')
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
