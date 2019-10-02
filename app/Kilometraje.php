<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Kilometraje extends Model
{
	protected $table = 'kilometrajes';
	// Relación
	public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}
	public function vehiculo() {
		return $this->belongsTo('App\Vehiculo', 'vehiculo_id');
	}
}
