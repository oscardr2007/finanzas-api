<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Apersona extends Model
{
	protected $table = 'apersonas';
	// Relación
	public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}	
}
