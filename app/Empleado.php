<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
	protected $table = 'empleados';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}