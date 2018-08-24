<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCortesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('cortes', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('solicitud_id');
          $table->char('listado_corte',10);
          $table->unsignedInteger('listado_id');
          $table->unsignedInteger('user_id');
          $table->timestamps();
          // llaves foráneas
          $table->foreign('solicitud_id')->references('id')->on('solicitudes');
          $table->foreign('user_id')->references('id')->on('users');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('cortes', function(Blueprint $table){
        $table->dropForeign([
          'solicitud_id',
          'user_id'
        ]);
      });
      Schema::dropIfExists('cortes');
    }
}
