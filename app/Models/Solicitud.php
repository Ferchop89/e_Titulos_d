<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $primary_key = 'solicitud_id';


    protected $casts = [
      'pasoACorte' => 'boolean',
      'citatorio' => 'boolean',
      'cancelada' => 'boolean'
    ];


    // public function status()
    // {
    //     return $this->hasOne('StatusSolicitud','id','status_id');
    // }

    public function procedencias(){
      // recupara las procedencias para un tipo de solicitud
      return $this->hasManyThrough(
        'App\Models\Procedencia',   // modelo final
        'App\Models\User',          // modelo intermedio
        'id',
        'id'
      );
    }

}
