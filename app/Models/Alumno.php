<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// class Alumno extends Authenticatable
// {
//    protected $connection = 'condoc_eti';
//    protected $primaryKey = 'num_cta';
//    protected $table = 'alumnos';
//
//    public function isAdmin()
//    {
//         return false;
//    }
// }

class Alumno extends Model implements AuthenticatableContract, AuthorizableContract
{
   use Authenticatable, Authorizable;

   protected $connection = 'condoc_eti';
   protected $primaryKey = 'num_cta';
   protected $table = 'alumnos';

   public function isAdmin()
   {
        return false;
   }
}
