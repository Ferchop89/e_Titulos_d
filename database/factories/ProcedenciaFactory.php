<?php

use Faker\Generator as Faker;
use App\Models\Procedencia;

$factory->define(Procedencia::class, function (Faker $faker) {
    static $number = 1;
    return [
      'procedencia' => $number < 10 ? 'Escuela_0'.$number++ : 'Escuela_'.$number++ ,
    ];
});
