<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product model.
 *
 * @package App\Models
 */
class ProductModel extends BaseModel
{
    use SoftDeletes;

    // Default order
    const DEFAULT_ORDER = 500;

    // Promo
    const GROUP_ID_PROMO = -1;

    // Pager
    const DEFAULT_PER_PAGE = 25;

    // Images
    const CERTIFICATES_GROUP_NAME = 'prodCertificates';
    const IMAGES_GROUP_NAME = 'prodImages';

    // Promo
    const PROMO_DAILY_PRODUCT = 'DP'; // Продукт дня
    const PROMO_WEEKLY_PRODUCT = 'WP'; // Продукт недели
    const PROMO_NO = ''; // Нет никакого промо
    const PROMOTIONS = [ self::PROMO_NO, self::PROMO_DAILY_PRODUCT,
        self::PROMO_WEEKLY_PRODUCT  ];

    // Sortings
    const SORT_ORDER = 'order';
    const SORT_NAME = 'name';
    const SORT_SIM = 'sim';
    const SORT_KEYS = [
        self::SORT_ORDER,
        self::SORT_NAME,
        self::SORT_SIM,
    ];

    // Unexplored
    const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $table = 'products';

    protected $hidden = [
        'deletedAt'
    ];

    protected $fillable = [
        'active',
        'name',
        'description',
        'imageId',
        'unitId',
        'unitStep',
        'price',
        'bonusesPercentage',
        'promotion',
        'groupId',
        'certificates',
        'tags',
        'order',
        'composition',
        'shelfLife',
        'nutrition',
        'netCostPerStep',
        'realUnits',
    ];

    protected $dates = [
        'deletedAt',
    ];
}
