<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
	protected $table = 'solicitud_refaccion';
	// Relación
	public function solicitud() {
		return $this->belongsTo('App\Solicitud', 'solicitud_id');
	}
	public function refaccion() {
		return $this->belongsTo('App\Refaccion', 'refaccion_id');
	}	
}