<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryIntervalModel extends BaseModel
{
    use SoftDeletes;

    const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $table = 'deliveryIntervals';

    protected $hidden = [
        'deletedAt',
        'createdAt',
        'updatedAt'
    ];

    protected $fillable = [
        'active',
        'dayOfWeek',
        'startHour',
        'startMinute',
        'endHour',
        'endMinute',
    ];

    protected $dates = [
        'deletedAt',
    ];
}
