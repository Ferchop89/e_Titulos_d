<?php

use Illuminate\Database\Seeder;
use App\Models\Web_Service;

class Web_Service_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = new Web_Service();
        $table->nombre = 'trayectoria';
        $table->descripcion = 'Consulta datos relacionados con la trayectoria de cada alumno';
        $table->key = 'He seguido la trayectoria en la que he creido y he confiado en mi mismo / Antonio Saura';
        $table->save();

        $table= new Web_Service();
        $table->nombre = 'identidad';
        $table->descripcion = 'Consulta datos relacionados con la información personal de cada alumno';
        $table->key = 'Nadie puede definir tu identidad, tu personalidad. Al fin y al cabo cada uno es responsable de quién y como es / Chinogizbo';
        $table->save();
    }
}
