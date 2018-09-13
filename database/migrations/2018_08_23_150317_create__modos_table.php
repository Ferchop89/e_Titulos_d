<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_modos', function (Blueprint $table) {
            $table->char('cat_subcve', 2)->unique();
            $table->string('cat_nombre');
            $table->char('ID_MODALIDAD_TITULACION',1);
            $table->string('MODALIDAD_TITULACION');
            $table->string('TIPO_DE_MODALIDAD');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_modos');
    }
}
