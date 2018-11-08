<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;
use App\Models\Procedencia;

class UserController extends Controller
{
    public function usuarios()
    {
      $users = User::paginate(8);
      $title = 'Listado de Usuarios';
      return view('users.lista_usuarios', compact('title','users'));
    }

    public function ver_usuario(User $user)
    {
      // dd($user); // para comprobar resultados. si aparece el usuario
      return view('users.ver_usuario', compact('user'));
    }

    public function crear_usuario()
    {
        // Se agregan los roles para crearlos dinámicamente vista de nuevos usuarios (create)
        $roles = Role::orderBy('id','asc')->get();
        return view('users.crear_usuario',['roles'=>$roles]);
    }

    public function store()
    {
        $data = request()->validate([
          'name' => 'required',
          'username' => ['required','min:6','unique:users,username'],
          'email' => ['required','email','unique:users,email'],
          'password' => ['required','min:6'],
          'procedencia_id' => '',
          'Admin' => '',
          'FacEsc' => '',
          'AgUnam' => '',
          'Jud' => '',
          'Sria' => '',
          'JSecc' => '',
          'JArea' => '',
          'Ofisi' => '',
          'Invit' => '',
          ],[
          'name.required' => 'El campo nombre es obligatorio',
          'username.required' => 'El alias mínimo es de 6 caracteres',
          'username.unique' => 'Este alias ya ha sido utilizado',
          'email.required' => 'El campo email es obligatorio',
          'email.email' => 'El campo email no es valido',
          'email.unique' => 'Este correo ya ha sido utilizado',
          'password.required' => 'El campo password es obligatorio',
          'password.min' => 'El password minimo es de 6 caracteres'
        ]);
        //$data = request()->all(); //Con esto funcional la prueba pero no la validacion

        $user = new User();
        $user->name =  $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        // Si FacEsc tiene check, se coloca el valor de procedencia, sino, nada
        $user->procedencia_id = isset($_POST['FacEsc']) ? request()->input('procedencia_id') : '';
        $user->save();

        // borramos todos los roles asociados en la tabla role_table
        $user->roles()->detach();

        // verificamos si se encuentra verificada la casilla entonces lo asociamos a la tabla pivote
        if( isset($_POST['Admin'])) { $user->roles()->attach( $_POST['Admin'] ); }
        // if( isset($_POST['FacEsc'])) { $user->roles()->attach( $_POST['FacEsc'] ); }
        // if( isset($_POST['AgUnam'])) { $user->roles()->attach( $_POST['AgUnam'] ); }
        // if( isset($_POST['Jud'])) { $user->roles()->attach( $_POST['Jud'] ); }
        // if( isset($_POST['Sria'])) { $user->roles()->attach( $_POST['Sria'] ); }
        // if( isset($_POST['JSecc'])) { $user->roles()->attach( $_POST['JSecc'] ); }
        // if( isset($_POST['JArea'])) { $user->roles()->attach( $_POST['JArea'] ); }
        // if( isset($_POST['Ofisi'])) { $user->roles()->attach( $_POST['Ofisi'] ); }
        if( isset($_POST['Director'])) { $user->roles()->attach( $_POST['Director'] ); }
        if( isset($_POST['SecGral'])) { $user->roles()->attach( $_POST['SecGral'] ); }
        if( isset($_POST['Rector'])) { $user->roles()->attach( $_POST['Rector'] ); }
        if( isset($_POST['Jtit'])) { $user->roles()->attach( $_POST['Jtit'] ); }
        $user->roles()->attach( '9' ); // por omision, el usuario tiene el rol de invitado

      return redirect()->route('users');  // redireccionamos al listado de usuarios

    }

    function editar_usuario(User $user, Procedencia $procede)
    {
        return view('users.editar_usuario',['user'=> $user, $procede]);
    }

    function update(User $user)
    {
        // $data = request()->all(); // no se debe usar
        // dd($data);
        $data = request()->validate([
          'name' => 'required',
          'username' => ['required','min:6'],
          'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
          'password' => '',
          'is_active' => ''
          ],[
            'name.required' => 'El campo nombre es obligatorio',
            'username.required' => 'El campo alias obligatorio',
            'username.min' => 'El alias minimo es de 6 caracteres',
            'email.required' => 'El campo email es obligatorio',
            'email.email' => 'El campo email no es valido',
            'email.unique' => 'Este correo ya ha sido utilizado',
          ]
        );

        // $selectedID = request()->input('procedencia_id');
        // $data['procedencia_id'] = $selectedID;

        if($data['password'] != null){
            $data['password'] = bcrypt($data['password']);
        } else {
              unset($data['password']);
        }

        // dd($data);
        // el usuario es activo?
        if( isset($_POST['is_active'])) { $data['is_active']=true ; } else { $data['is_active']=false ;  }

        // borramos todos los roles asociados en la tabla role_table
        $user->roles()->detach();

        // verificamos si se encuentra verificada la casilla entonces lo asociamos a la tabla pivote
        if( isset($_POST['Admin'])) { $user->roles()->attach( $_POST['Admin'] ); }
        // if( isset($_POST['FacEsc'])) { $user->roles()->attach( $_POST['FacEsc'] ); }
        // if( isset($_POST['AgUnam'])) { $user->roles()->attach( $_POST['AgUnam'] ); }
        // if( isset($_POST['Jud'])) { $user->roles()->attach( $_POST['Jud'] ); }
        // if( isset($_POST['Sria'])) { $user->roles()->attach( $_POST['Sria'] ); }
        // if( isset($_POST['JSecc'])) { $user->roles()->attach( $_POST['JSecc'] ); }
        // if( isset($_POST['JArea'])) { $user->roles()->attach( $_POST['JArea'] ); }
        // if( isset($_POST['Ofisi'])) { $user->roles()->attach( $_POST['Ofisi'] ); }
        if( isset($_POST['Director'])) { $user->roles()->attach( $_POST['Director'] ); }
        if( isset($_POST['SecGral'])) { $user->roles()->attach( $_POST['SecGral'] ); }
        if( isset($_POST['Rector'])) { $user->roles()->attach( $_POST['Rector'] ); }
        if( isset($_POST['Jtit'])) { $user->roles()->attach( $_POST['Jtit'] ); }
        $user->roles()->attach( '9' ); // por omision, el usuario tiene el rol de invitado

        $user->procedencia_id = isset($_POST['FacEsc']) ? request()->input('procedencia_id') : null;
        $user->update($data);

       // return redirect("usuarios/{$user->id}");
       return redirect()->route('users.ver_usuario',['user'=>$user]); // Eloquet toma el Id por lo que se pudo especificar explicitamente $user->id
    }

    function destroy(User $user)
    {
        // borramos todos los roles asociados en la tabla role_table
        $user->roles()->detach();

        $user->delete();
        return redirect()->route('users'); // equivalente a la ruta 'usuarios'
    }

}
