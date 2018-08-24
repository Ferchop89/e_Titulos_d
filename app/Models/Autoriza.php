<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autoriza extends Model
{
    protected $table = '_autorizaciones';
    protected $primaryKey = 'ID_AUTORIZACION_RECONOCIMIENTO'; // or null    
    public $incrementing = false;
    public $timestamps = false;
}
