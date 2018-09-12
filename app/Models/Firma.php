<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firma extends Model
{
    protected $table = '_firmas';
    protected $primaryKey = 'ID_CARGO'; // or null    
    public $incrementing = false;
    public $timestamps = false;
}
