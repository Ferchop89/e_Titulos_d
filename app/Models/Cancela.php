<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cancela extends Model
{
    protected $table = '_cancelaciones';
    protected $primaryKey = 'ID_MOTIVO_CAN'; // or null    
    public $incrementing = false;
    public $timestamps = false;

}
