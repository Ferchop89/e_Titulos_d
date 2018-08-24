<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->increments('id');
            $table->text('cuenta');
            $table->text('nombre');
            $table->unsignedInteger('escuela_id');
            $table->unsignedInteger('tipo');
            $table->boolean('citatorio')->defaul(false);
            $table->boolean('pasoACorte')->default(false);
            $table->boolean('cancelada')->default(false);
            $table->unsignedInteger('user_id');
            $table->timestamps();
            // Llaves foraneas
            $table->index(['pasoACorte']);
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
        Schema::table('solicitudes', function(Blueprint $table){
            $table->dropForeign([
              'user_id',
            ]);
        });
        Schema::dropIfExists('solicitudes');
    }
}
