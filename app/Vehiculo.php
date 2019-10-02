<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
	protected $table = 'vehiculos';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}