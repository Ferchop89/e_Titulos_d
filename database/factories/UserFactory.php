<?php

use Faker\Generator as Faker;
use App\Models\User;


// $factory->define(App\User::class, function (Faker $faker) {
$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'username' => str_random(6),
        'email' => $faker->unique()->safeEmail,
        'procedencia_id' => rand(2,20),
        'password' => bcrypt('111111'), // secret
        'remember_token' => str_random(10),
        'is_active' => rand(0,1),
    ];
});
