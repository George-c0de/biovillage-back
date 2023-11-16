<?php

use Illuminate\Database\Seeder;

class DevSeeder extends Seeder {
    public function run() {
        $this->call(DevClientsSeeder::class);
        $this->call(DevSuperAdminSeeder::class);
        $this->call(ComponentsSeeder::class);
    }
}
