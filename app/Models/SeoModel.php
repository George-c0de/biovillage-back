<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Модель для роботы с сео настройками
 * сео настройки могут задаваться для любой сущьности на сайте
 * для этого необходимо в моделе сущьности задать константу GROUP_NAME
 * и записать значение этой константы и id записи
 */
class SeoModel extends BaseModel {
    public $timestamps = false;
    protected $connection = 'main';
    protected $table = 'seo';

    protected $fillable = [
        'entityId',
        'groupName',
        'seoTitle',
        'seoDescription',
        'ogLocale',
        'ogType',
        'ogSiteName',
        'ogTitle',
        'ogDescription',
        'ogUrl',
        'ogImage',
        'ogVideo',
    ];
}
