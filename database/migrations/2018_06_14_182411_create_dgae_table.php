<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDgaeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dgae', function (Blueprint $table) {
            $table->increments('id');
            $table->char('listado_corte',10);
            $table->unsignedInteger('listado_id');
            $table->unsignedInteger('user_id');
            $table->dateTime('Solicitado_at');
            $table->dateTime('Recibido_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cortes');
    }
}
