<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Storage;

/**
 * Модель коллекций для компонентов
 */
class ComponentCollectionModel extends BaseModel {
    public $timestamps = false;
    protected $connection = 'main';
    protected $table = 'componentCollections';

    protected $fillable = [
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];
}
