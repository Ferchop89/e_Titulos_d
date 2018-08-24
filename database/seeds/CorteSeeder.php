<?php

use App\Models\Solicitud;
use App\Models\Corte;
use App\Models\User;

use Carbon\Carbon;

use Illuminate\Database\Seeder;

class CorteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      $users_count = User::count();
      // $solicitudes_count = (int)(Solicitud::count()-100);
      $solicitudes_count = (int)Solicitud::count();
      $corte = Carbon::create(2018, 5, 1, 13, 0, 0, 'America/Mexico_City');
      for ($i=1; $i <= $solicitudes_count;) {
        $list_id = 1;
        do {
            // Encontramos la solicitud id consecutivo
            $registro = Solicitud::where('id',$i)->first();
            $creacion = $registro->created_at;
            // Registramos una nueva revisión y actualizamos la Solicitud
            if ( !($registro->cancelada == true or $registro->tipo==0 ) ){
              $registro->pasoACorte = true;
              $registro->save();
              $rev_est = new Corte();
                $rev_est->solicitud_id = $registro->id;
                $rev_est->listado_corte = $registro->created_at->format("d.m.Y");
                $rev_est->listado_id = $list_id;
                $rev_est->user_id=rand(1,$users_count);
                $rev_est->save();
            }
            $i++;
        } while ($creacion->lt($corte) and $i<= $solicitudes_count);
        $corte->addDays(1);
      }
    }
}
