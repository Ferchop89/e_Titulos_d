<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutTransInfo extends Model
{
   protected $connection = 'condoc_eti';
   protected $table = 'ati';
   protected $fillable = [
      'id',
      'nombre_completo',
      'curp',
      'tel_fijo',
      'tel_celular',
      'correo',
      'autoriza'
   ];
}
