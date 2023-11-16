<?php

use Illuminate\Database\Seeder;

class ProdSeeder extends Seeder {
    public function run() {
        $this->call(SuperAdminSeeder::class);
        $this->call(ComponentsSeeder::class);
    }
}
