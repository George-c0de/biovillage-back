<?php

namespace App\Models\Auth;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Класс для авторизации админов
 * Class Admin
 * @package App\Models\Auth
 */
class Admin extends Authenticatable {
    use HasApiTokens, Notifiable;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    protected $connection = 'main';
    protected $table = 'admins';
    protected $guard = 'admin';

    public $hidden = [
        'password',
        'deletedAt'
    ];

    public $fillable = [
        'name',
        'phone',
        'roles',
        'password'
    ];

    protected $dates = [
    ];
}
