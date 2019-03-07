<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InfoExtra extends Authenticatable
{
   // protected $guard = 'students';
   protected $dates = ['ingreso_laboral'];

   protected $connection = 'condoc_eti';
   protected $primaryKey = 'id';
   protected $table = 'info_extra';

}
