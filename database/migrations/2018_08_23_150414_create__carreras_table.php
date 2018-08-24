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
            $table->char('CVE_INSTITUCION');
            $table->string('NOMBRE_INSTITUCIÓN');
            $table->string('TIPO_DE_SOSTENIMIENTO');
            $table->string('TIPO_DE_SOSTENIMIENTO');
            $table->string('TIPO_EDUCATIVO');
            $table->string('NIVEL_DE_ESTUDIOS');
            $table->string('CVE_CARRERA');
            $table->string('CARRERA');
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
