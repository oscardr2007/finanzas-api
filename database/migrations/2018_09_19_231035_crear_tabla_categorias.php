<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaCategorias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('categorias')) {

            Schema::create('categorias', function (Blueprint $table) {
                $table->engine='InnoDB';
                $table->charset='utf8';
                $table->collation='utf8_general_ci';
                $table->increments('id');           
                $table->integer('user_id')->unsigned()->nullable();                
                $table->string('descripcion', 255);                             
                $table->char('estatus', 1);                                      
                $table->rememberToken();
                $table->timestamps();  
            });

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias');
    }
}
