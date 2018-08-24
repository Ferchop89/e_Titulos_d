<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Legal extends Model
{
    protected $table = '_legales';
    protected $primaryKey = 'ID_FUNDAMENTO_LEGAL_SERVICIO_SOCIAL'; // or null    
    public $incrementing = false;
    public $timestamps = false;
}
