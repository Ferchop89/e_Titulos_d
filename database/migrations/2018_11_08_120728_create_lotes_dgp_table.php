<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotesDgpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lotes_dgp', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lote_unam_id');
            $table->unsignedInteger('user_id');
            /*Carga*/
            $table->unsignedInteger('lote_dgp');
            $table->string('msj_carga')->nullable();
            $table->DateTime('fecha_carga')->nullable();
            /*Consulta*/
            $table->unsignedInteger('estatus');
            $table->string('msj_consulta')->nullable();
            $table->DateTime('fecha_consulta')->nullable();
            /*Descarga*/
            $table->text('archivo_descarga');
            $table->text('ruta_descarga');
            $table->string('msj_descarga')->nullable();
            $table->DateTime('fecha_descarga')->nullable();


            $table->timestamps();

            //Llaves foraneas
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('lote_unam_id')->references('id')->on('lotes_unam');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lotes_dgp');
    }
}
