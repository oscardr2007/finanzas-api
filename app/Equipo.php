<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
	protected $table = 'equipos';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}
