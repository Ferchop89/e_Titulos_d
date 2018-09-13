<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_entidades', function (Blueprint $table) {
            $table->char('pais_cve', 5)->unique();
            $table->string('pais_nombre');
            $table->char('pais_cve_ch', 2);
            $table->char('ID_ENTIDAD_FEDERATIVA',2);
            $table->string('C_NOM_ENT');
            $table->string('C_ENTIDAD_ABR');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_entidades');
    }
}
