<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudesCanceladasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_canceladas', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';
            $table->increments('id');
            $table->string('num_cta', 9);
            $table->string('nombre_completo', 200);
            $table->string('nivel', 2);
            $table->string('cve_carrera', 10);
            $table->DateTime('fecha_cancelacion');
            $table->unsignedInteger('id_motivoCan');
            //Llaves foráneas
            $table->foreign('id_motivoCan')->references('id')->on('_cancelacionesSep');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes_canceladas');
    }
}
