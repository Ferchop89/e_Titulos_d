<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos', function (Blueprint $table) {
           $table->charset = 'utf8';
           $table->collation = 'utf8_spanish_ci';
           $table->string('num_cta', 9);
           $table->string('password');
           $table->string('apellido1');
           $table->string('apellido2');
           $table->string('nombres');
           $table->string('curp')->nullable();
           $table->string('tel_fijo')->nullable();
           $table->string('tel_celular')->nullable();
           $table->string('correo')->nullable();
           $table->boolean('autoriza')->default(false);
           $table->boolean('activo')->default(true);
           $table->date('fecha_nac');
           $table->string('ip_usuario', 15)->nullable();
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos');
    }
}
