<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
	protected $table = 'diagnosticos';

    // Relación

    public function solicitud() {
    	return $this->belongsTo('App\Solicitud', 'solicitud_id');
    }
}