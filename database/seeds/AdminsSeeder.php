<?php

use App\Helpers\DbHelper;
use App\Models\Auth\Admin;
use App\Service\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Propaganistas\LaravelPhone\PhoneNumber;

class AdminsSeeder extends Seeder {
    public function run() {
        $phoneTemplate = 89112001100;
        foreach(AdminRole::ROLES as $role) {
            $newAdminData = [
                'name' => ucfirst($role),
                'phone' => PhoneNumber::make($phoneTemplate++, env('PHONE_COUNTRY', 'RU'))->formatE164(),
                'password' => Hash::make('pass' . $role . '1'),
                'roles' => DbHelper::arrayToPgArray([$role]),
            ];
            $newAdmin = new Admin($newAdminData);
            $newAdmin->saveOrFail();

            $this->command->line($newAdminData['name'] . ' created');
            $this->command->line('Phone: ' . $newAdminData['phone']);
            $this->command->line('Password: ' . 'pass' . $role . '1');
        }
    }
}
