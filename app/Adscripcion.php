<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adscripcion extends Model
{
	protected $table = 'adscripciones';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}