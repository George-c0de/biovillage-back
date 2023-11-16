<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SliderModel extends Model
{
    use SoftDeletes;

    const GROUP_NAME = 'slider';

    const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $table = 'sliders';

    protected $fillable = [
        'name',
        'description',
        'bgColor',
        'order',
    ];

    protected $hidden = [
        'deletedAt'
    ];

    protected $dates = [
        'deletedAt',
    ];
}
