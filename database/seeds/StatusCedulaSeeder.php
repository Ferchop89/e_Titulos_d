<?php

use Illuminate\Database\Seeder;
use App\Models\StatusCedula;

class StatusCedulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $table = new StatusCedula();
      $table->id = '01';
      $table->descripcion = 'Validado';
      $table->save();

      $table = new StatusCedula();
      $table->id = '02';
      $table->descripcion = 'Paso a firma';
      $table->save();
    }

}
