<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSolicitudes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('solicitudes')) {

            Schema::create('solicitudes', function (Blueprint $table) {
                $table->engine='InnoDB';
                $table->charset='utf8';
                $table->collation='utf8_general_ci';
                $table->increments('id');           
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->integer('servicio_id')->unsigned()->nullable();
                $table->foreign('servicio_id')->references('id')->on('servicios');
                $table->integer('adscripcion_id')->unsigned()->nullable();
                $table->foreign('adscripcion_id')->references('id')->on('adscripciones');
                $table->integer('empleado_id')->unsigned()->nullable();
                $table->foreign('empleado_id')->references('id')->on('empleados');                
                $table->integer('equipo_id')->unsigned()->nullable();                    
                $table->string('descripcion', 255);
                $table->date('fecha');
                $table->time('hora');
                $table->char('indicador_equipo',1);                
                $table->char('estatus',1);
                $table->float('ubicacion_lt', 16, 8);
                $table->float('ubicacion_ln', 16, 8);  

                $table->date('fecha_diag');
                $table->time('hora_diag');
                $table->string('descripcion_diag', 255);
                $table->date('fecha_diag_entrega');
                $table->time('hora_diag_entrega');

                $table->date('fecha_ser');
                $table->time('hora_ser');
                $table->string('utilizados_ser', 255);
                $table->string('descripcion_ser', 255);
                $table->string('observaciones_ser', 255);
                $table->char('licenciamiento_ser', 1);
                $table->char('winoriginal_ser', 1);
                $table->char('ofioriginal_ser', 1);
                $table->integer('cantidad_ser');
                $table->char('evaluacion_ser', 1);

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
        Schema::dropIfExists('solicitudes');
    }
}
