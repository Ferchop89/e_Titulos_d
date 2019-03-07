<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotesUnam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('lotes_unam', function (Blueprint $table) {
          $table->charset = 'utf8';
          $table->collation = 'utf8_spanish_ci';
          $table->increments('id');
          $table->DateTime('fecha_lote');
          $table->unsignedInteger('status')->default(1);
          $table->boolean('firma0')->default(false);
          $table->DateTime('fec_firma0')->nullable();
          $table->boolean('firma1')->default(false);
          $table->DateTime('fec_firma1')->nullable();
          $table->text('cert1')->nullable();
          $table->boolean('firma2')->default(false);
          $table->DateTime('fec_firma2')->nullable();
          $table->boolean('firma3')->default(false);
          $table->DateTime('fec_firma3')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
