<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class TagModel extends BaseModel
{
    use SoftDeletes;

    const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $table = 'tags';

    protected $hidden = [
        'deletedAt'
    ];

    protected $fillable = [
        'name',
        'order',
        'active',
    ];

    protected $dates = [
        'deletedAt',
    ];
}
