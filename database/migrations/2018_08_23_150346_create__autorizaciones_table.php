<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutorizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_autorizaciones', function (Blueprint $table) {
            $table->char('ID_AUTORIZACION_RECONOCIMIENTO',1)->unique();
            $table->string('AUTORIZACION_RECONOCIMIENTO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_autoriza');
    }
}
