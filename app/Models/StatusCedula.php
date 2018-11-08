<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StatusCedula extends Authenticatable
{
   // protected $guard = 'students';

   protected $connection = 'condoc_eti';
   protected $table = '_status_cedula';
   public $timestamps = false;

}
