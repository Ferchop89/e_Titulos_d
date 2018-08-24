<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudio extends Model
{
    protected $table = '_estudios';
    protected $primaryKey = 'ID_TIPO_ES'; // or null    
    public $incrementing = false;
    public $timestamps = false;
}
