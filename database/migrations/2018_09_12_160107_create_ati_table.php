<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ati', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';
            $table->increments('id');
            $table->string('nombre_completo');
            $table->string('curp');
            $table->string('tel_fijo');
            $table->string('tel_celular');
            $table->string('correo');
            $table->boolean('autoriza')->default(false);
            $table->string('num_cta', 9);
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
        Schema::dropIfExists('ati');
    }
}
