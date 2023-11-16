<?php

use Illuminate\Database\Seeder;
use App\Models\Auth\Client;

class DevClientsSeeder extends Seeder {
    public function run() {
        factory(Client::class, 10)->create();
    }
}
