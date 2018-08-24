<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('menus', function (Blueprint $table) {
           $table->increments('id');
           $table->string('name', 150);
           $table->string('slug', 150)->unique();
           $table->string('ruta',50)->default('/');
           $table->unsignedInteger('parent')->default(0);
           $table->smallInteger('order')->default(0);
           $table->boolean('is_structure');
           $table->boolean('enabled')->default(1);
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
        Schema::dropIfExists('menus');
    }
}
