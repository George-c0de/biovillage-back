<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Order model.
 *
 * @package App\Models
 */
class OrderItemModel extends BaseModel
{


    public $timestamps = false;

    protected $table = 'orderItems';

    protected $fillable = [
        'realPrice',
        'realUnits',
        'realTotal',
    ];

}
