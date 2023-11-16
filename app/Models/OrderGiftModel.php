<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Order model.
 *
 * @package App\Models
 */
class OrderGiftModel extends BaseModel
{

    public $timestamps = false;

    protected $table = 'orderGifts';

    protected $fillable = [
        'realQty',
        'realTotalBonuses'
    ];

}
