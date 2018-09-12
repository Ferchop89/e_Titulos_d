<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = '_entidades';
    protected $primaryKey = 'ID_ENTIDAD'; // or null
    public $incrementing = false;
    public $timestamps = false;

}
