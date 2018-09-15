<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
          'web_services',
          'procedencias',
          'users',
          'roles',
          'role_user',
          'menus',
          '_autorizaciones',
          '_cancelaciones',
          '_entidades',
          '_carreras',
          '_estudios',
          '_firmas',
          '_legales',
          '_modos',
          'solicitudes_sep',
          '_codigos_error_feu'
      ]);
      // En este orden porque los roles deben existir antes que los usuarios
      $this->call(Web_Service_Seeder::class);
        $this->call(CatalogosSepSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ProcedenciaSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MenuSeeder::class);

        $this->call(SolicitudesSepSeeder::class);
        $this->call(CodigosErrorFEUSeeder::class);
    }

    public function truncateTables(array $tables){
      DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
      foreach ($tables as $table) {
          DB::table($table)->truncate();
      }
      DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

}
