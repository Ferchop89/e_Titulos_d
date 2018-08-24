<?php

use Illuminate\Database\Seeder;
use App\Models\Procedencia;

class ProcedenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // El primer registro de procedencia es UNAM
      $procede =    new Procedencia();
      $procede->procedencia = 'UNAM';
      $procede->save();
      // Registros de procedencia Facultad o Escuela
      factory(Procedencia::class,19)->create();
    }
}
