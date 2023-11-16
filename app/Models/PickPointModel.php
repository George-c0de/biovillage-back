<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Addresses model.
 *
 * @package App\Models
 */
class PickPointModel extends BaseModel
{
    use SoftDeletes;

    public const SORT_NAME = 'name';
    public const SORT_ADD = 'createdAt';
    public const SORTS = [ self::SORT_NAME, self::SORT_ADD];

    protected $table = 'pickPoints';

    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'name',
        'address',
        'lat',
        'lon',
        'workDays',
        'contacts',
        'isActive'
    ];

    protected $dates = [
        'deletedAt',
    ];
}
