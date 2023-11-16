<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryAreaModel extends BaseModel
{
    use SoftDeletes;

    const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $table = 'deliveryArea';

    protected $hidden = [
        'deletedAt'
    ];

    protected $fillable = [
        'name',
        'active',
        'price',
        'color',
        'deliveryFreeSum'
    ];

    protected $dates = [
        'deletedAt',
    ];
}
