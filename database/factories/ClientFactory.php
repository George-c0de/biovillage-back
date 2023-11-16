<?php

use App\Models\Auth\Client;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'phone' => $faker->numerify('79#########'),
        'email' => $faker->email,
    ];
});
