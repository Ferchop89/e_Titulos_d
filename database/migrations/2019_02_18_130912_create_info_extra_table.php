<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoExtraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_extra', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';

            $table->increments('id');
            $table->string('codigo_postal', 5);
            $table->string('estado');
            $table->string('municipio');
            $table->string('colonia');
            $table->string('calle_numero');
            $table->unsignedInteger('labora');
            $table->string('lugar_laboral')->nullable();
            $table->string('cargo_laboral')->nullable();
            $table->date('ingreso_laboral')->nullable();
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
        Schema::dropIfExists('info_extra');
    }
}
