<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotesUnam extends Model
{
   protected $casts = [
     'firma0' => 'boolean',
     'firma1' => 'boolean',
     'firma2' => 'boolean',
     'firma3' => 'boolean'
   ];

    protected $table = 'lotes_unam';
    public $timestamps = false;
}
