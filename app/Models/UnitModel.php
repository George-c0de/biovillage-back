<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UnitModel extends BaseModel
{
    use SoftDeletes;

    const DELETED_AT = 'deletedAt';

    public $timestamps = false;

    protected $table = 'units';

    protected $hidden = [
        'deletedAt'
    ];

    protected $fillable = [
        'fullName',
        'shortName',
        'shortDerName',
        'step',
        'factor',
    ];

    protected $dates = [
        'deletedAt',
    ];
}
