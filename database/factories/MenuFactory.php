<?php

use Faker\Generator as Faker;
use App\Models\Menu;

$factory->define(Menu::class, function (Faker $faker) {
  $name = $faker->name;
  $menus = Menu::all();
  return [
      'name' => $name,
      'slug' => str_slug($name),
      'parent' => (count($menus) > 0) ? $faker->randomElement($menus->pluck('id')->toArray()) : 0,
      'order' => 0
  ];
});
