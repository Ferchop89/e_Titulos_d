<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrosDGP extends Model
{
   protected $table = 'registros_dgp';
   protected $primaryKey = array('lote_dgp', 'num_cta');
   public $incrementing = false;
   protected $fillable = array('lote_dgp', 'num_cta');
}
