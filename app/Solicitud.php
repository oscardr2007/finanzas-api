<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
	protected $table = 'solicitudes';
	// Relación
	public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}
	public function servicio() {
		return $this->belongsTo('App\Servicio', 'servicio_id');
	}
	public function adscripcion() {
		return $this->belongsTo('App\Adscripcion', 'adscripcion_id');		
	}
	public function empleado() {
		return $this->belongsTo('App\Empleado', 'empleado_id');		
	}
	public function equipo() {
		return $this->belongsTo('App\Equipo', 'equipo_id');		
	}
}