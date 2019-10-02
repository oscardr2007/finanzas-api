<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreaTablaEquipos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('equipos')) {

            Schema::create('equipos', function (Blueprint $table) {
                $table->engine='InnoDB';
                $table->charset='utf8';
                $table->collation='utf8_general_ci';
                $table->increments('id');           
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->string('descripcion', 255);
                $table->string('marca', 255);
                $table->string('modelo', 255);         
                $table->date('fecha_adq');
                $table->string('serie', 255);
                $table->string('inventario', 255);
                $table->char('estatus', 1);
                $table->string('ubicacion', 255); 
                $table->float('ubicacion_lt', 16,2);
                $table->float('ubicacion_ln', 16, 2);           
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
        Schema::dropIfExists('equipos');
    }
}
