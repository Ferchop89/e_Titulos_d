<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosgradoUnamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_posgradoUnam', function (Blueprint $table) {
           $table->charset = 'utf8';
           $table->collation = 'utf8_spanish_ci';
           $table->string('clave_carrera_UNAM', 7);
           $table->string('clave_orientacion_UNAM',2);
           $table->string('nombre_carrera_UNAM',100);
           $table->string('nombre_orientacion_UNAM',100);
           $table->string('clave_carrera_SEP',7);
           $table->string('nombre_carrera_SEP',100);
           $table->string('nivel',1);
           $table->string('grado_masculino',110);
           $table->string('grado_femenino',110);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('_posgradoUnam', function (Blueprint $table) {
            //
        });
    }
}
