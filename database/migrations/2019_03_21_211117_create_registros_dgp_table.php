<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrosDgpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registros_dgp', function (Blueprint $table) {
            $table->integer('lote_dgp')->unsigned();
            $table->string('num_cta', 9);
            $table->integer('ESTATUS');
            $table->string('NOMBRE_ARCHIVO', 40);
            $table->string('DESCRIPCION', 255);
            $table->string('FOLIO_CONTROL', 30);
            $table->timestamps();

            // integridad
            $table->foreign('lote_dgp')->references('id')->on('lotes_dgp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registros_dgp');
    }
}
