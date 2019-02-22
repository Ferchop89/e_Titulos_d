<?php

use Illuminate\Database\Seeder;
use App\Models\StatusDgp;

class StatusDgpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $table = new StatusDgp();
      // $table->id = 1;
      $table->nombreCorto = '01. Pendiente Envío';
      $table->descripcion = 'Lote no enviado a la DGP';
      $table->save();
    }

}
