<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancelacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_cancelacionesSep', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ID_MOTIVO_CAN');
            $table->string('DESCRIPCION_CANCELACION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_cancelacionesSep');
    }
}
