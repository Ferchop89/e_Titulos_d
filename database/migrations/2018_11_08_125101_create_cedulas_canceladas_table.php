<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCedulasCanceladasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cedulas_canceladas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lote_unam_id');
            $table->unsignedInteger('lote_dgp_id');
            $table->string('folio_control');
            $table->unsignedInteger('motivo_id');
            $table->unsignedInteger('codigo'); //0 = Exitoso, 1 = Error
            $table->string('mensaje')->nullable();
            $table->DateTime('fecha_cancelacion')->nullable();
            $table->unsignedInteger('user_id');
            //Para considerar información del alumno
            $table->string('num_cta', 9);
            $table->timestamps();
            //Llaves foraneas
            $table->foreign('lote_unam_id')->references('id')->on('lotes_unam');
            $table->foreign('lote_dgp_id')->references('id')->on('lotes_dgp');
            $table->foreign('motivo_id')->references('id')->on('_cancelacionesSep');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cedulas_canceladas');
    }
}
