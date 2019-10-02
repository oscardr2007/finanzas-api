<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refaccion extends Model
{
	protected $table = 'refacciones';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}