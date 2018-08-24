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

          // Agregamos 49 usuarios fake
          factory(User::class,49)->create();
          // Le damos mas peso al role de FacEsc
          $roles_w = [2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,3,4,5,6,7,8,9];
          // Les agregamos hasta cinco roles de forma aleatoria
          for ($i=0; $i < 5 ; $i++) {
            $users = User::all();
            foreach ($users as $user) {
              $RandRole = $roles_w[rand(0,count($roles_w)-1)];
              if ($user->roles()->where('role_id',$RandRole)->count()===0 && rand(0,1) ){
                $user->roles()->attach($RandRole);
              }
            }
          }
          //Actualizamos procedencia_id usuarios que no tengan FacEsc
          $users = User::all();
          foreach ($users as $user) {
            if (!$user->hasRole('FacEsc')) {
              $user->procedencia_id = '1'; // UNAM
              $user->save();
            }
          }
    }
}
