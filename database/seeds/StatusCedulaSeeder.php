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
      // $table->id = 1;
      $table->nombreCorto = '01.Solicitud';
      $table->descripcion = 'Solicitud de cédula profesional electrónica';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 2;
      $table->nombreCorto = '02.Autorizado Jtit';
      $table->descripcion = 'Autorizado por el depto. de Títulos';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 3;
      $table->nombreCorto = '03.Firma Jtit';
      $table->descripcion = 'Firmado por el depto. de Títulos';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 4;
      $table->nombreCorto = '04.Firma DGAE';
      $table->descripcion = 'Firmado por la Directora DGAE';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 5;
      $table->nombreCorto = '05.Enviado DGP';
      $table->descripcion = 'Enviado a la Dirección General de Profesiones';
      $table->save();

      // $table = new StatusCedula();
      // // $table->id = 5;
      // $table->nombreCorto = '05.Firma Srio';
      // $table->descripcion = 'Firmado por el Secretario General';
      // $table->save();
      //
      // $table = new StatusCedula();
      // // $table->id = 6;
      // $table->nombreCorto = '06.Firma Rector';
      // $table->descripcion = 'Firmado por el Rector';
      // $table->save();
      //
      // $table = new StatusCedula();
      // // $table->id = 7;
      // $table->nombreCorto = '07.Enviado DGP';
      // $table->descripcion = 'Enviado a la Dirección General de Profesiones';
      // $table->save();
    }

}
