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
      $table->nombreCorto = '01.Carga Solicitudes';
      $table->descripcion = 'Pasaron de títulos a solicitudes';
      $table->secuencia = 'Pendientes de revisión';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 2;
      $table->nombreCorto = '02.Revisa JUD';
      $table->descripcion = 'Revisados por JUD';
      $table->secuencia = 'Pendientes de autorización JUD.';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 3;
      $table->nombreCorto = '03.Autoriza JUD';
      $table->descripcion = 'Autorizados JUD';
      $table->secuencia = 'Pendientes de firma DGAE';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 4;
      $table->nombreCorto = '04.Firma DGAE';
      $table->descripcion = 'Firmados DGAE';
      $table->secuencia = 'Pendientes de envio a DGP';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 5;
      $table->nombreCorto = '05.Enviado DGP';
      $table->descripcion = 'Enviados a DGP';
      $table->secuencia = 'Pendientes de descarga DGP';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 6;
      $table->nombreCorto = '06.Descarga DGP';
      $table->descripcion = 'Descargados DGP';
      $table->secuencia = 'Pendientes de aprobación DGP';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 7;
      $table->nombreCorto = '07.TER en DGP';
      $table->descripcion = 'Título Electrónico Rechazado';
      $table->secuencia = 'Pendiente de corrección';
      $table->save();

      $table = new StatusCedula();
      // $table->id = 8;
      $table->nombreCorto = '08.TEA en DGP';
      $table->descripcion = 'Título Electrónico Aprobado';
      $table->secuencia = 'Título Electrónico Aprobado';
      $table->save();
    }

}
