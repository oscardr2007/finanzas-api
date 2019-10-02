<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAdscripciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('adscripciones')) {

            Schema::create('adscripciones', function (Blueprint $table) {
                $table->engine='InnoDB';
                $table->charset='utf8';
                $table->collation='utf8_general_ci';
                $table->increments('id');           
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->char('codigo', 12);
                $table->string('descripcion', 255);
                $table->string('direccion', 255);
                $table->char('lada', 3);   
                $table->char('telefono', 10);                     
                $table->float('ubicacion_lt', 16,8);
                $table->float('ubicacion_ln', 16, 8);           
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
        Schema::dropIfExists('adscripciones');
    }
}
