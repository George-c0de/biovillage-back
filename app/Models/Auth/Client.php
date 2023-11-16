<?php

namespace App\Models\Auth;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Класс для авторизации обычных клиентов
 *
 * @package App\Models\Auth
 */
class Client extends Authenticatable {
    use HasApiTokens, Notifiable;

    public const DEFAULT_PER_PAGE = 25;

    // Sort
    public const SORT_NAME = 'name';
    public const SORT_LAST_LOGIN = 'lastLoginAt';
    public const SORT_REG = 'createdAt';
    public const SORT_KEYS = [ self::SORT_NAME, self::SORT_LAST_LOGIN,
        self::SORT_REG];

    // Platforms
    public const PLATFORM_IOS = 'iOS';
    public const PLATFORM_ANDROID = 'Android';
    public const PLATFORM_SITE = 'Site';
    public const PLATFORM_NA = 'na';
    public const PLATFORMS = [
        self::PLATFORM_ANDROID,
        self::PLATFORM_IOS,
        self::PLATFORM_NA,
        self::PLATFORM_SITE
    ];

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    protected $table = 'clients';
    protected $guard = 'client';

    protected $dates = [
        'deletedAt',
    ];
    protected $hidden = [
        'deletedAt',
    ];

    protected $fillable = [
        'name',
        'email',
        'birthday',
        'allowMailing'
    ];

    const IMAGE_GROUP_NAME = 'clients';

    /**
     * Get order model by id
     * @param $client
     * @return Client
     */
    public static function clientInstance($client)
    {
        if (!$client instanceof Client) {
            $client = Client::findOrFail($client);
        }
        return $client;
    }

}
