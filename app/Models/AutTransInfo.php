<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AutTransInfo extends Authenticatable
{
   // protected $guard = 'students';

   protected $connection = 'condoc_eti';
   protected $primaryKey = 'num_cta';
   protected $table = 'ati';

}
