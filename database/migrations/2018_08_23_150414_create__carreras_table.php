<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarrerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_carreras', function (Blueprint $table) {
            $table->string('CVE_OFICIAL');
            $table->char('CVE_INSTITUCION', 6);
            $table->string('NOMBRE_INSTITUCION');
            $table->string('TIPO_DE_SOSTENIMIENTO');
            $table->string('TIPO_EDUCATIVO');
            $table->string('NIVEL_DE_ESTUDIOS');
            $table->string('CVE_SEP', 6);
            $table->string('CARRERA');

            // Llave Primaria Compuesta.
            $table->primary(['CVE_INSTITUCION','CVE_SEP']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_carreras');
    }
}
