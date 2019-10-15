<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
	protected $table = 'nacionalidades';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}