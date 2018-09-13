<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_estudios', function (Blueprint $table) {
            $table->char('cat_subcve', 2)->unique();
            $table->string('cat_nombre');
            $table->char('ID_TIPO_ESTUDIO_ANTECEDENTE');
            $table->string('TIPO_ESTUDIO_ANTECEDENTE');
            $table->string('EDUCACION_SUPERIOR');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudios');
    }
}
