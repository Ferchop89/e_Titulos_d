<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class Menu extends Model
{

  protected $casts = [
    'is_structure' => 'boolean',
  ];

  public function getChildren($data, $line)
  {
      $children = [];
      foreach ($data as $line1) {
          if ($line['id'] == $line1['parent']) {
            // Se omiten los submenus vacions que son estructuras
            if ( !($line1['is_structure'] and $this->getChildren($data, $line1)==[]) ) {
              $children = array_merge($children, [ array_merge($line1, ['submenu' => $this->getChildren($data, $line1) ]) ]);
            }
          }
      }
      return $children;
  }

  public function optionsMenu()
  {
    // devuelve los 12 registros de la tabla  menu
    // el usuario ya se encuentra logueado, por lo que ya podemos
    // obtener rutas y roles.

    $data = new Menu();
    $xitems = $data->items();  // rutas y roles autorizadas

    $data_return = $this->where('enabled', 1)
        ->where('is_structure',1)
        ->orwhereIn('ruta',$xitems)
        ->orderby('parent')
        ->orderby('order')
        ->orderby('name')
        ->get()
        ->toArray();
    return $data_return;
  }

  public static function menus()
  {
      // si el usuario no esta logeado, no hay menu.
      if (!Auth::check()) {
        return [];
      }
      $menus = new Menu();

      $data = $menus->optionsMenu();

      $menuAll = [];
      foreach ($data as $line) {
          $item = [ array_merge($line, ['submenu' => $menus->getChildren($data, $line) ]) ];
          $menuAll = array_merge($menuAll, $item);
      }

      $menus->menuAll = $menuAll;
      // filtramos los conjuntos que esten vacios y que sean una estructura.
      return $menus->menuAll;
  }

  public function uyr(){
    // consultamos todas las rutas que tienen autorizados el usuario loggeado
    $user = Auth::user();
    $roles = [];
    foreach ($user->roles as $role) {
      $roles[] = $role->nombre;
    }
    return $roles;
  }

  public function ryr()
  {
    // Procedimiento que retorna todas las rutas y sus roles asociados
    $rutas = collect(Route::getRoutes());
    // dd($rutas);
    $arreglorutas = $rutas->toArray();
    $rutasyroles = [];
    for ($i=0 ; $i < count($arreglorutas) ; $i++ ) {
      // $ruta = collect($arreglorutas[$i])->toArray();
      $ruta = collect($arreglorutas[$i])->toArray();
      $ruta_action = $ruta['action'];
      if (array_key_exists('as',$ruta_action) and array_key_exists('roles',$ruta_action)) {
          $rutayrol = array($ruta_action['as'] => $ruta_action['roles']);
          $rutasyroles = array_merge($rutasyroles,$rutayrol);
        }
    }
    return $rutasyroles;
  }

  public function loguser()
  {
    // Se actualiza manualmente una cuenta para probar el procedimiento.
    Auth::logout();
    Auth::attempt(['username' => 'Administrador', 'password' => 'Admon4974'],false);
  }

  public static function items(){
      // Si el usuario no esta logeado, no hay items de menu.
      if (!Auth::check()) {
        return [];
      }

      $datos = new Menu();
      // $datos->loguser();
      $rutas = $datos->ryr();
      // dd($rutas);
      $roles = $datos->uyr();
      $itemsarr = [];
      // if (isset($value['sub'][$pg]))
      foreach ($rutas as $key => $value) {
        // key es la ruta que hay que salvar si el usuario tiene un rol asociados
        foreach ($roles as $valor) {
          // dd($valor,$rutas[$key]);
          if (in_array($valor,$rutas[$key])) {
            // Si no se encuentra repetida la ruta..
            if (!in_array($key,$itemsarr)) {
              $itemsarr[] = $key;
            }
          }
        }
      }
      return $itemsarr;
  }

}
