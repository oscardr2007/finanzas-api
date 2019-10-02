<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
	protected $table = 'categorias';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}