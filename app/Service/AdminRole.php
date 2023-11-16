<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Models\Auth\Admin;
use App\Service\BaseService;
use Illuminate\Foundation\Auth\User;

/**
 * Ролевая модель админов проекта
 */
class AdminRole extends BaseService
{
    /**
     * Супер пользователь - имеет полный доступ к системе, может управлять другими админами.
     */
    const SUPERADMIN_ROLE = 'superadmin';

    /**
     * Администратор - имеет полный доступ к системе, может управлять администраторами с другими ролями.
     */
    const ADMIN_ROLE = 'admin';

    /**
     * Оператор - работает с заказами, пользователями.
     */
    const OPERATOR_ROLE = 'operator';

    /**
     * Закупщик - собирает и формирует заказ на след. день.
     */
    const PURCHASER_ROLE = 'purchaser';

    /**
     * Комплектовщик - просматривает список заказов на день, отмечает сумму закупки.
     */
    const PACKER_ROLE = 'packer';

    /**
     * Доставщик - просматривает список заказов на день доставки, отмечает доставленные.
     */
    const DELIVERY_ROLE = 'delivery';

    /**
     * Доставщик - просматривает список заказов на день доставки, отмечает доставленные.
     */
    const STOREKEEPER_ROLE = 'storekeeper';

    /**
     * Роли системы
     */
    const ROLES = [
        self::SUPERADMIN_ROLE,
        self::ADMIN_ROLE,
        self::OPERATOR_ROLE,
        self::PURCHASER_ROLE,
        self::PACKER_ROLE,
        self::DELIVERY_ROLE,
        self::STOREKEEPER_ROLE,
    ];

    /**
     * @param $admin
     * @param $hasRoles array Строка со значениями через запятую.
     *
     * @return bool
     */
    public static function hasRole($admin, $hasRoles)
    {

        if (empty($admin['roles'])) return false;

        $adminRoles = DbHelper::pgArrayToArray($admin->roles);

        // Если в массиве ролей есть AdminRole::SUPERADMIN_ROLE, то сразу разрешаем ему всё.
        if (in_array(self::SUPERADMIN_ROLE, $adminRoles)) {
            return true;
        }

        $result = false;

        // Массив ролей в БД у админа должен содержать все роли передаваемые в параметр $hasRoles функции
        foreach ($hasRoles as $hasRole) {
            if (in_array($hasRole, $adminRoles)) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
