<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
	protected $table = 'servicios';
	// Relación
	public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}
	public function categoria() {
		return $this->belongsTo('App\Categoria', 'categoria_id');
	}
}