<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
	protected $table = 'documentos';

    // Relación

    public function user() {
    	return $this->belongsTo('App\User', 'user_id');
    }
}