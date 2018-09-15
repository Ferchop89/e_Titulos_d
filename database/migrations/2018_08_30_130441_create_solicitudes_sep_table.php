<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudesSepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_sep', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';
            $table->increments('id');
            $table->string('num_cta', 9);
            $table->string('nombre_completo', 200);
            $table->string('nivel', 2);
            $table->string('cve_carrera', 10);
            $table->string('cve_registro_sep', 10);
            $table->unsignedInteger('user_id');
            $table->timestamps();
            //Llaver foraneas
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
        Schema::dropIfExists('solicitudes_sep');
    }
}
