<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = '_carreras';
    protected $primaryKey = array('CVE_INSTITUCION', 'CVE_SEP');
    public $incrementing = false;
    public $timestamps = false;
}
