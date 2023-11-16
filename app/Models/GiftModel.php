<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product model.
 *
 * @package App\Models
 */
class GiftModel extends BaseModel
{
    use SoftDeletes;

    // Default order
    const DEFAULT_ORDER = 500;

    // Pager
    const DEFAULT_PER_PAGE = 25;

    // Images
    const CERTIFICATES_GROUP_NAME = 'gifts';
    const IMAGES_GROUP_NAME = 'giftImages';

    // Sortings
    const SORT_ORDER = 'order';
    const SORT_NAME = 'name';
    const SORT_KEYS = [ self::SORT_ORDER, self::SORT_NAME ];

    // Unexplored
    const DELETED_AT = 'deletedAt';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public $timestamps = false;

    protected $table = 'gifts';

    protected $hidden = [
        'deletedAt'
    ];

    protected $fillable = [
        'active',
        'name',
        'description',
        'composition',
        'shelfLife',
        'price',
        'order',
    ];

    protected $dates = [
        'deletedAt',
    ];
}
