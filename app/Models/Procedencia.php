<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedencia extends Model
{
  protected $fillable = [
      'procedencia',
    ];

    public function solicitudes(){
      // recupara las solicitudes para un tipo de procedencia
      return $this->hasManyThrough(
        'App\Models\Solicitud',
        'App\Models\User',
        'procedencia_id',
        'user_id',
        'id',
        'id'
      );
    }
    public function users(){
      //  Usuarios que pertenecen a una Procedencia...
          return $this->hasMany('App\Models\User');
    }

}
