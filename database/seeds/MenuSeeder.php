<?php

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
       {
         // seed del menú. genera opciones de rutas para Títulos
           //Administración de usuarios
           $m1 = factory(Menu::class)->create([
               'name' => 'Usuarios',
               'slug' => 'opcion1',
               'ruta' => '#',
               'parent' => 0,
               'order' => 0,
               'is_structure' => 1
           ]);
           //Departamento de Títulos
           $m2 = factory(Menu::class)->create([
               'name' => 'Títulos',
               'slug' => 'opcion2',
               'ruta' => '#',
               'parent' => 0,
               'order' => 1,
               'is_structure' => 1
           ]);
           //Departamento de Títulos consultas
           $m3 = factory(Menu::class)->create([
               'name' => 'Consultas',
               'slug' => 'opcion3',
               'ruta' => '#',
               'parent' => 0,
               'order' => 2,
               'is_structure' => 1
           ]);

           // Opciones de Submenú...
           //Para adminitración de usuarios
           factory(Menu::class)->create([
               'name' => 'Ver usuarios',
               'slug' => 'opcion-1.1',
               'parent' => $m1->id,
               'ruta' => 'admin/usuarios',
               'order' => 0,
               'is_structure' => 0
           ]);
           factory(Menu::class)->create([
               'name' => 'Crear usuario',
               'slug' => 'opcion-1.2',
               'ruta' => 'admin/usuarios/nuevo',
               'parent' => $m1->id,
               'order' => 1,
               'is_structure' => 0
           ]);
           // --- administración de usuarios
           //Para Departamento de Títulos
           $m100 = factory(Menu::class)->create([
               'name' => 'Cargar solicitudes',
               'slug' => 'opcion-2.1',
               'ruta' => '',
               'parent' => $m2->id,
               'order' => 0,
               'is_structure' => 1
           ]);
           factory(Menu::class)->create([
               'name' => 'Por fecha',
               'slug' => 'opcion-2.1.1',
               'parent' => $m100->id,
               'ruta' => 'registroTitulos/buscar/fecha',
               'order' => 0,
               'is_structure' => 0
           ]);
           factory(Menu::class)->create([
               'name' => 'Por número de cuenta',
               'slug' => 'opcion-2.1.2',
               'parent' => $m100->id,
               'ruta' => 'registroTitulos/buscar',
               'order' => 1,
               'is_structure' => 0
           ]);
           $m200 = factory(Menu::class)->create([
               'name' => 'Solicitud de firma',
               'slug' => 'opcion-2.2',
               'ruta' => '',
               'parent' => $m2->id,
               'order' => 1,
               'is_structure' => 1
           ]);
           factory(Menu::class)->create([
               'name' => 'Revisión y aprobación',
               'slug' => 'opcion-2.2.1',
               'parent' => $m200->id,
               'ruta' => 'registroTitulos/lista-solicitudes/pendientes',
               'order' => 0,
               'is_structure' => 0
           ]);
           $m300 = factory(Menu::class)->create([
             'name' => 'Proceso de Firma',
             'slug' => 'opcion-2.3',
             'ruta' => '',
             'parent' => $m2->id,
             'order' => 2,
             'is_structure' => 1
           ]);
           factory(Menu::class)->create([
               'name' => 'Firmar solicitudes',
               'slug' => 'opcion-2.3.1',
               'parent' => $m300->id,
               'ruta' => 'registroTitulos/response/firma',
               'order' => 0,
               'is_structure' => 0
           ]);
           $m400 = factory(Menu::class)->create([
               'name' => 'Consultas',
               'slug' => 'opcion-3.1',
               'ruta' => '',
               'parent' => $m3->id,
               'order' => 0,
               'is_structure' => 1
           ]);
           factory(Menu::class)->create([
               'name' => 'Firmas solicitadas',
               'slug' => 'opcion-3.2',
               'parent' => $m400->id,
               'ruta' => 'registroTitulos/firmas_busqueda/seleccion',
               'order' => 0,
               'is_structure' => 0
           ]);
           factory(Menu::class)->create([
               'name' => 'Progreso de firmas',
               'slug' => 'opcion-3.3',
               'parent' => $m400->id,
               'ruta' => 'registroTitulos/firmas_progreso',
               'order' => 1,
               'is_structure' => 0
           ]);
           factory(Menu::class)->create([
               'name' => 'Firmas enviadas a la DGP',
               'slug' => 'opcion-2.4.3',
               'parent' => $m400->id,
               'ruta' => 'registroTitulos/firmas_enviadas',
               'order' => 2,
               'is_structure' => 0
           ]);
           factory(Menu::class)->create([
               'name' => 'Solicitudes firmadas',
               'slug' => 'opcion-2.4.4',
               'parent' => $m400->id,
               'ruta' => 'registroTitulos/firmadas',
               'order' => 3,
               'is_structure' => 0
           ]);
           factory(Menu::class)->create([
               'name' => 'Solicitudes canceladas',
               'slug' => 'opcion-2.4.5',
               'parent' => $m400->id,
               'ruta' => 'registroTitulos/solicitudes_canceladas',
               'order' => 4,
               'is_structure' => 0
           ]);
           //Departamento de Títulos consultas
           $m4 = factory(Menu::class)->create([
               'name' => 'DGP',
               'slug' => 'opcion4',
               'ruta' => '#',
               'parent' => 0,
               'order' => 3,
               'is_structure' => 1
           ]);
           $m500 = factory(Menu::class)->create([
               'name' => 'DGP',
               'slug' => 'opcion-4.1',
               'ruta' => '',
               'parent' => $m4->id,
               'order' => 0,
               'is_structure' => 1
            ]);
           factory(Menu::class)->create([
               'name' => 'Envio de cédulas a DGP',
               'slug' => 'opcion-4.1.1',
               'parent' => $m500->id,
               'ruta' => 'registroTitulos/cedulas_DGP',
               'order' => 0,
               'is_structure' => 0
           ]);
           //Departamento de Títulos consultas
           $m5 = factory(Menu::class)->create([
               'name' => 'Tablero de control',
               'slug' => 'opcion5',
               'ruta' => '#',
               'parent' => 0,
               'order' => 4,
               'is_structure' => 1
           ]);
           $m600 = factory(Menu::class)->create([
               'name' => 'Gráficas',
               'slug' => 'opcion-5.1',
               'ruta' => '',
               'parent' => $m5->id,
               'order' => 0,
               'is_structure' => 1
            ]);
            factory(Menu::class)->create([
                'name' => 'Gestión de solicitudes',
                'slug' => 'opcion-5.1.1',
                'parent' => $m600->id,
                'ruta' => 'registroTitulos/cedulasG',
                'order' => 1,
                'is_structure' => 0
            ]);
       }
}
