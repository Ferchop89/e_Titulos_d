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
          $table->DateTime('fechaLote');
          $table->text('cadenaFirma1')->nullable();
          $table->text('cadenaFirma2')->nullable();
          $table->text('cadenaFirma3')->nullable();
          $table->text('firma1')->nullable();
          $table->text('firma2')->nullable();
          $table->text('firma3')->nullable();
          $table->text('xml')->nullable();
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
        //
    }
}
