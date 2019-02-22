<?php

use Illuminate\Database\Seeder;

class RUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      /* DEPARTAMENTO DE REVISIÓN DE ESTUDIOS PROFESIONALES Y DE POSGRADO */

      $user = new User();
      $user->name = 'Lic. H. Laura Castillo Díaz';
      $user->username = 'LauraCD';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Jud')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Irene Mendoza Ruelas';
      $user->username = 'IreneMR';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Sria')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Adriana Hernández Méndez';
      $user->username = 'AdrianaHM';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JArea')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Karla Paola Zavaleta Téllez';
      $user->username = 'KarlaZT';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JArea')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Marisol Elizabeth Rebollo Mosco';
      $user->username = 'MarisonRM';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JArea')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Martín Edmundo Galicia Carpio';
      $user->username = 'MartinGC';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JArea')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Norma Hernández García';
      $user->username = 'NormaHG';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JSecc')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Elvira Medina Reyes';
      $user->username = 'ElviraMR';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JSecc')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Andrés Reyes Amador';
      $user->username = 'AndresRA';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JSecc')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Angélica López Patiño';
      $user->username = 'AngelicaLP';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi03')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'María Ofelia Sedano Ramos';
      $user->username = 'MariaSR';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi03')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Camelia Matínez Carrizosa';
      $user->username = 'CameliaMC';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Enriqueta Hernández Peña';
      $user->username = 'EnriquetaHP';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi03')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Juana Stefani Torres Vera';
      $user->username = 'JuanaTV';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi03')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Lydia Mervich González Ruíz';
      $user->username = 'LydiaGR';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JSecc')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Olivia Aranda Chávez';
      $user->username = 'OliviaAC';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','JSecc')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Luis Enrique González Molina';
      $user->username = 'LuisGM';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'David Feliciano Jiménez Cruz';
      $user->username = 'DavidJC';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'María Alicia Ortiz Cortéz';
      $user->username = 'MariaOC';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Karla Liliana Fuentes Lozada';
      $user->username = 'KarlaFL';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'María Guadalupe Alvarez González';
      $user->username = 'MariaAG';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Jesús Adán Ordoñez Martínez';
      $user->username = 'JesusOM';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Maura Leticia Serrano Castro';
      $user->username = 'MauraSC';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Diego González Chávez';
      $user->username = 'DiegoGC';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

      $user = new User();
      $user->name = 'Jesús Camacho Zugarazo';
      $user->username = 'JesusCZ';
      $user->procedencia_id = '10001';
      $user->password = bcrypt('123456');
      $user->is_active = true;
      $user->save();
      $role=Role::where('nombre','Ofisi08')->first();
      $user->roles()->attach($role);

    }
}
