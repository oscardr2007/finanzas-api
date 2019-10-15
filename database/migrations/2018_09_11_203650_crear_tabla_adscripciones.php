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
                $table->increments('id');           
                $table->integer('user_id');                
                $table->string('codigo', 16);
                $table->string('descripcion', 80);                                                 
                $table->string('ingresa_facturas', 1);
                $table->string('autorizado_facturas', 60);          
                $table->string('estatus',1); 
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
