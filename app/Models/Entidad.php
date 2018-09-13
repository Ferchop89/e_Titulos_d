<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = '_entidades';
    protected $primaryKey = 'pais_cve'; // or null
    public $incrementing = false;
    public $timestamps = false;

}
