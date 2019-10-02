<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Bpersona extends Model
{
	protected $table = 'bpersonas';
	// Relación
	public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}	
}