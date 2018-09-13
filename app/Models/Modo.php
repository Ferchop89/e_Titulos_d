<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modo extends Model
{
    protected $table = '_modos';
    protected $primaryKey = 'cat_subcve'; // or null
    public $incrementing = false;
    public $timestamps = false;
}
