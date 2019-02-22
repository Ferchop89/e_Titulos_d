<?php

use App\Models\Alumno;
use Illuminate\Database\Seeder;

class CasoEspecialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $alumno = new Alumno();
      $alumno->num_cta = '517014994';
      $alumno->password = bcrypt('22051990');
      $alumno->apellido1 = 'GAVIDIA';
      $alumno->apellido2 = 'CARRANZA';
      $alumno->nombres = 'JESSICA IVETTE';
      $alumno->curp = 'GACJ900522MNEVRS06';
      $alumno->tel_fijo = '555562009718';
      $alumno->tel_celular = '5562009718';
      $alumno->correo = 'jessicagavidia@gmail.com';
      $alumno->autoriza = 0;
      $alumno->activo = 1;
      $alumno->fecha_nac = '1990-05-22';
      $alumno->ip_usuario = NULL;
      $alumno->save();
    }
}
