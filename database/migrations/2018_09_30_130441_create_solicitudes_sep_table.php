<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudesSepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_sep', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_spanish_ci';
            $table->increments('id');
            $table->string('num_cta', 9);
            $table->string('nombre_completo', 200);
            $table->string('nivel', 2);
            $table->string('cve_carrera', 10);
            $table->DateTime('fec_emision_tit');
            $table->string('libro', 4);
            $table->string('foja', 4);
            $table->string('folio', 15);
            $table->text('datos')->nullable();
            $table->text('errores')->nullable();
            $table->text('paridad')->nullable(); // paridad de catalogos UNAM-SEP
            $table->unsignedInteger('status')->default(1);
            $table->unsignedInteger('fecha_lote_id')->nullable();
            $table->DateTime('fecha_lote')->nullable();
            $table->text('firma0')->default('');
            $table->text('firma1')->default('');
            $table->text('firma2')->default('');
            $table->text('firma3')->default('');
            $table->DateTime('tit_fec_DGP')->nullable();
            $table->char('sistema')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('ws_ati'); //0 no esta en ATI, 1 para ATI, 2 no esta en WS, 3 WS
            $table->timestamps();
            //Llaves foraneas
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('status')->references('id')->on('_status_cedula');
            $table->foreign('fecha_lote_id')->references('id')->on('lotes_unam');
            // $table->foreign('nivel')->references('cat_subcve')->on('_estudios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes_sep');
    }
}
