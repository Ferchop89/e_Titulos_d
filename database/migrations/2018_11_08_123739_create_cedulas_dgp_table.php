<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCedulasDgpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cedulas_dgp', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lote_unam_id');
            $table->unsignedInteger('lote_dgp_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('estatus');
            $table->string('descripcion');
            $table->string('folio_control');
            $table->timestamps();
            //Llaves foraneas
            $table->foreign('lote_unam_id')->references('id')->on('lotes_unam');
            $table->foreign('lote_dgp_id')->references('id')->on('lotes_dgp');
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
        Schema::dropIfExists('cedulas_dgp');
    }
}
