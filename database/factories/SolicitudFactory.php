<?php

use Faker\Generator as Faker;
use App\Models\Solicitud;
use App\Models\User;

$factory->define(Solicitud::class, function (Faker $faker) {
    $cuenta = User::all()->count();
    return [
        'cuenta' =>  int_random(9),
        'nombre' => $faker->name,
        'escuela_id' => int_random(1,count($procedencias)),
        'user_id' => int_random(1,$cuenta),
        'promocion' => int_random(0,1),
    ];
});
