<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudio extends Model
{
    protected $table = '_estudios';
    protected $primaryKey = 'cat_subcve'; // or null    
    public $incrementing = false;
    public $timestamps = false;
}
