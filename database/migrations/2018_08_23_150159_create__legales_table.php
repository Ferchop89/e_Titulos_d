<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_legales', function (Blueprint $table) {
            $table->char('ID_FUNDAMENTO_LEGAL',1)->unique();
            $table->string('FUNDAMENTO_LEGAL_SERVICIO_SOCIAL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_legales');
    }
}
