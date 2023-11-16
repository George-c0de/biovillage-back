<?php

use App\Helpers\DbHelper;
use App\Models\Auth\Admin;
use App\Service\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevSuperAdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run() {
        $email = 'admin@admin.ru';
        $password = 'secret';
        $admin = new Admin([
            'name' => 'Супер админ',
         //   'email' => $email, // нет такого поля в бд
            'phone' => '89112001100',
            'password' => Hash::make($password),
            'roles' => DbHelper::arrayToPgArray([AdminRole::SUPERADMIN_ROLE]),
        ]);
        $admin->saveOrFail();

        $this->command->line("superadmin created");
        $this->command->line("email: $email");
        $this->command->line("password: $password");
    }
}
