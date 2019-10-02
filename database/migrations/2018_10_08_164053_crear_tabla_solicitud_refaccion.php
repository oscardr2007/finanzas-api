<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSolicitudRefaccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('solicitud_refaccion')) {

            Schema::create('solicitud_refaccion', function (Blueprint $table) {
                $table->engine='InnoDB';
                $table->charset='utf8';
                $table->collation='utf8_general_ci';                        
                $table->integer('solicitud_id')->unsigned()->nullable();
                $table->foreign('solicitud_id')->references('id')->on('solicitudes');  
                $table->integer('refaccion_id')->unsigned()->nullable();
                $table->foreign('refaccion_id')->references('id')->on('refacciones');
                $table->integer('cantidad');
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
        Schema::dropIfExists('solicitud_refaccion');
    }
}
