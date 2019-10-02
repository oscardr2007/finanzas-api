<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
	protected $table = 'rutas';
	// Relación
	public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}	
}