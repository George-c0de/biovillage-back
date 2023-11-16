<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Subscribe model.
 *
 * @package App\Models
 */
class SubscribeModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'subscribes';

    public $timestamps = false;

    protected $hidden = [
    ];

    protected $fillable = [
    ];
}
