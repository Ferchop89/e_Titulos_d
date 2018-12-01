<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusCedulaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('_status_cedula', function (Blueprint $table) {
          $table->charset = 'utf8';
          $table->collation = 'utf8_spanish_ci';
          $table->increments('id');
          $table->string('nombreCorto', 20);
          $table->text('descripcion');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_status_cedula');
    }
}
