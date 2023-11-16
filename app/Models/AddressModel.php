<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Addresses model.
 *
 * @package App\Models
 */
class AddressModel extends BaseModel
{
    use SoftDeletes;

    public const SORT_NAME = 'name';
    public const SORT_ADD = 'createdAt';
    public const SORTS = [ self::SORT_NAME, self::SORT_ADD];

    protected $table = 'addresses';

    const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $hidden = [
        'clientId',
        'deletedAt'
    ];

    protected $fillable = [
        'clientId',
        'city',
        'street',
        'house',
        'building',
        'entrance',
        'floor',
        'doorphone',
        'appt',
        'lat',
        'lon',
        'comment',
        'name'
    ];

    protected $dates = [
        'deletedAt',
    ];
}
