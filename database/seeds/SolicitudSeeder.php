<?php

use Illuminate\Database\Seeder;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Procedencia;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;
use Faker\Factory as Faker;


class SolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // Agrega desde el inicio de 2018 hasta el dia actual, de 8 hasta las 19 horas 150 solicitudes cada 1, 2, o tres minutos
        // factory(Solicitud::class,10)->create();
        $pesoCancelada = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1];
        $pesoTipo = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0];

        // creamos un arreglo con los usuarios que no vienen de la UNAM
        $usuarios = User::where('procedencia_id','>','1')->pluck('id')->toArray();
        $procedencia = procedencia::where('id','>','1')->pluck('id')->toArray();
        $RegMin = 20; $RegMax = 50;
        $inicioSeed = Carbon::create(2018, 5, 1, 13, 0, 0, 'America/Mexico_City');
        $inicio = clone $inicioSeed;
        $cuenta = $inicioSeed->diffIndays(Carbon::now());
        $output = 0;

        for ($i=0; $i < $cuenta+1 ; $i++) {
            if ( !($inicio->isSaturday() or $inicio->isSunday()) ) {
              $laburo = clone $inicio;
              // $laburo->addHours(8);
              $Registros = rand($RegMin,$RegMax);
              for ($y=0; $y < $Registros; $y++) {
                     $laburo->addMinutes(600 / $Registros);
                     $faker = Faker::create();
                     $sol_rev = new Solicitud();
                     $xcuenta = '';for ($c = 0; $c<9; $c++) { $xcuenta .= mt_rand(0,9);}
                     $sol_rev->cuenta = $xcuenta;
                     $sol_rev->nombre = $faker->name;
                     $usuario_ale = $usuarios[rand(0,count($usuarios)-1)];
                     $sol_rev->user_id = $usuario_ale;
                     $sol_rev->escuela_id = $procedencia[rand(0,count($procedencia)-1)];
                     $tipoSol = $pesoTipo[rand(0,count($pesoTipo)-1)];
                     $sol_rev->tipo = $tipoSol;
                     $sol_rev->citatorio = ($tipoSol==0)? true : false ; // Si es tipo 0 (sin papeles) entoces citatorio es true
                     $sol_rev->pasoACorte = false;
                     $sol_rev->cancelada = $pesoCancelada[rand(0,count($pesoCancelada)-1)]; // 4 a 1 cumple vs citatorio
                     $sol_rev->created_at = $laburo;
                     $sol_rev->updated_at = $laburo;
                     $sol_rev->save();
                }
            }
            $inicio->addDays(1);
        }
    }
}
