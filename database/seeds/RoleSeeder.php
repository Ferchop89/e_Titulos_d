<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
      public function run()
      {
         $Admin =    new Role();$Admin->nombre = 'Admin';$Admin->descripcion = 'Administrador';$Admin->save();
         $FacEsc =   new Role();$FacEsc->nombre = 'FacEsc';$FacEsc->descripcion = 'Facultad o Escuela';$FacEsc->save();
         $AgUnam =   new Role();$AgUnam->nombre = 'AgUnam';$AgUnam->descripcion = 'Archivo Gral. UNAM';$AgUnam->save();
         $Jud =      new Role();$Jud->nombre = 'Jud';$Jud->descripcion = 'DCerConDoc';$Jud->save();
         $Sria =     new Role();$Sria->nombre = 'Sria';$Sria->descripcion = 'DCerConDoc';$Sria->save();
         $JSecc =    new Role();$JSecc->nombre = 'JSecc';$JSecc->descripcion = 'DCerConDoc';$JSecc->save();
         $JArea =    new Role();$JArea->nombre = 'JArea';$JArea->descripcion = 'DCerConDoc';$JArea->save();
         $Ofnista =  new Role();$Ofnista->nombre = 'Ofisi';$Ofnista->descripcion = 'DCerConDoc';$Ofnista->save();
         $Ofnista =  new Role();$Ofnista->nombre = 'Ofisi03';$Ofnista->descripcion = 'DCerConDoc';$Ofnista->save();
         $Ofnista =  new Role();$Ofnista->nombre = 'Ofisi08';$Ofnista->descripcion = 'DCerConDoc';$Ofnista->save();
         $Invitado = new Role();$Invitado->nombre = 'Invit';$Invitado->descripcion = 'Invitado';$Invitado->save();
         $Director = new Role();$Director->nombre = 'Director';$Director->descripcion = 'Director DGAE';$Director->save();
         $SecGral =  new Role();$SecGral->nombre = 'SecGral';$SecGral->descripcion = 'Secretario General';$SecGral->save();
         $Rector =   new Role();$Rector->nombre = 'Rector';$Rector->descripcion = 'Rector UNAM';$Rector->save();
         $Jtit =     new Role();$Jtit->nombre = 'Jtit';$Jtit->descripcion = 'DCerConDoc';$Jtit->save();
       }
}
