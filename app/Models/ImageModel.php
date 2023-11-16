<?php

namespace App\Models;

use App\Models\Auth\Client;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель для роботы с картинками
 * В данной моделе будут хранится картинки с отношением один ко многим
 * таблица может хранить и записывать картинки для разных сущьностей одновременно
 * для этого нужно 2 параметра
 * groupName он задается в каждой связной модели константой GROUP_NAME
 * entityId Это id сущьности к для которой мы хотим сохранить картинки
 */
class ImageModel extends BaseModel {

    use SoftDeletes;

    public const DELETED_AT = 'deletedAt';

    public $timestamps = false;
    protected $connection = 'main';
    protected $table = 'images';

    protected $fillable = [
        'entityId',
        'groupName',
        'src',
        'srcThumb',
        'isDeleted',
        'order',
    ];
}
