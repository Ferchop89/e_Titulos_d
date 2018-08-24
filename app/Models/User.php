<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $casts = [
      'is_active' => 'boolean',
    ];

    protected $fillable = [
        'name', 'username', 'email',  'password', 'is_active', 'procedencia_id',
      ];

      protected $hidden = [
          'password', 'remember_token',
      ];

      public function roles()
      {
        return $this->belongsToMany(Role::class)->withTimestamps();
      }

      public function procede()
      {
        return $this->hasOne('App\Models\Procedencia','id','procedencia_id');
      }

    public function hasAnyRole($roles)
    {
      if (is_array($roles))
      {
          foreach ($roles as $role) {
            if($this->hasRole($role)){
              return true;}
          }
        } else
        {
            if($this->hasRole($roles)){
              return true;}
        }
        return false;
      }

    /**
    * Check one role
    * $param string $role
    */
    public function hasRole($role)
    {
      if($this->roles()->where('nombre',$role)->first()) {
        return true;
      }
      return false;
    }

}
