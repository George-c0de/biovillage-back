<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupModel extends Model
{
    use SoftDeletes;

    const SORT_ORDER = 'order';
    const SORT_NAME = 'name';
    const SORT_CREATED = 'created';
    const SORTS = [ self::SORT_ORDER, self::SORT_NAME, self::SORT_CREATED ];

    const IMAGE_GROUP_NAME = 'catalogSection';

    protected $connection = 'main';
    protected $table = 'groups';

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $dates = [
        'deletedAt',
    ];

    protected $fillable = [
        'name',
        'active',
        'order',
        'bgColor',
    ];
}
