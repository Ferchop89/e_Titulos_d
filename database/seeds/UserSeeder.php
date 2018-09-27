<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          // Usuario patrón
          $user= new User();
          $user->name = 'Administrador';
          $user->username = 'Administrador';
          $user->email = 'Admon@correo.com';
          $user->procedencia_id = '1';
          $user->password = bcrypt('111111');
          $user->is_active = true;
          $user->save();
          $role=Role::where('nombre','Admin')->first();
          $user->roles()->attach($role);

          $user = new User();
          $user->name = '';
          $user->username = 'Directora';
          $user->email = NULL;
          $user->procedencia_id = '1';
          $user->password = bcrypt('d1rDGAE');
          $user->is_active = true;
          $user->save();
          $role=Role::where('nombre','Director')->first();
          $user->roles()->attach($role);

          $user = new User();
          $user->name = '';
          $user->username = 'Secretario';
          $user->email = NULL;
          $user->procedencia_id = '1';
          $user->password = bcrypt('SecgRaL');
          $user->is_active = true;
          $user->save();
          $role=Role::where('nombre','SecGral')->first();
          $user->roles()->attach($role);

          $user = new User();
          $user->name = '';
          $user->username = 'Rector';
          $user->email = NULL;
          $user->procedencia_id = '1';
          $user->password = bcrypt('R3CT0R');
          $user->is_active = true;
          $user->save();
          $role=Role::where('nombre','Rector')->first();
          $user->roles()->attach($role);
    }
}
