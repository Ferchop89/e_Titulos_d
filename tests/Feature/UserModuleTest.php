<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserModuleTest extends TestCase
{
    // ejecutamos este comando porque no lo esta ejecutando el trait RefreshDatabase;

    use RefreshDatabase; // Para la migracion y transaccion de las pruebas

    /**  @test */
//     function if_loads_the_user_list_page()
//     {
//        factory(User::class)->create([
//          'name'=>'Juan',
//          // 'website'=>'TheLastOfUs.net',
//        ]);
//        factory(User::class)->create([
//          'name'=>'Guillermo',
//        ]);
//
//         $this->get('/usuarios')
//             ->AssertStatus(200)
//             ->AssertSee('Usuarios')
//             ->AssertSee('Juan')
//             ->AssertSee('Guillermo');
//     }
//
//     /**  @test */
//      function if_lists_is_empty_send_default_mss()
//      {
//        //Se elimina esta linea puesto que estamos utilizado RefreshDatabase y usando transacciones
//        //DB::table('users')->truncate();   // Truncamos la tabla para vaciarla y probar
//
//          // $this->get('/usuarios?empty')  // este fue util para el entorno por arreglos
//          $this->get('/usuarios')  // este fue util para el entorno por arreglos
//              ->AssertStatus(200)
//              ->AssertSee('No hay usuarios registrados');
//      }
//
//     /** @test */
//     function it_displays_the_user_details()
//     {
//       # $this->withoutExceptionHandling();
//       // creamos un usuario con factory
//       $user = factory(User::class)->create([
//         'name' => 'Duilio Palacios'
//       ]);
//
//       $this->get("/usuarios/{$user->id}")  // usuarios/5
//         ->AssertStatus(200)
//         ->AssertSee('Duilio Palacios');
//     }
//
//     /** @test */
//     function it_display_error_404_if_the_user_is_not_found()
//     {
//       $this->get('/usuarios/999')
//             ->AssertStatus(404)
//             ->AssertSee('Página no encontrada');
//
//     }
//
//     /** @test */
//     function it_loads_new_user()
//     {
//       $this->get('/usuarios/nuevo')
//         ->AssertStatus(200)
//         ->AssertSee('Crear un nuevo usuario.');
//     }
//
//     /** @test */
//     function it_creates_a_new_user()
//     {
//
//       $this->withoutExceptionHandling(); // para verificar cual es el error
//
//       $this->post('/usuarios/', [
//         'name' => 'Duilio',
//         'username' => 'nduilio',
//         'email' => 'dustyle@styde.net',
//         'password' => '123456',        // dato obligatorio comprobar
//       ])->assertRedirect(route('users'));
//       // $this->assertDatabaseHas('users',[   // Comprobacion cuando no se tienen credenciales
//
//         $this->assertCredentials([
//           'name' => 'Duilio',
//           'username' => 'nduilio',
//           'email' => 'dustyle@styde.net',
//           'password' => '123456',
//       ]);
//     }
//
//     /** @test */
//     function it_updates_a_user()
//     {
//
//       $user=factory(User::class)->create(); // generamos un registro de nombres aleatorios que van a ser actualizados
//
//       // $this->withoutExceptionHandling(); // para verificar cual es el error
//
//       $this->put("/usuarios/{$user->id}", [
//         'name' => 'Duilio',
//         'username' => 'nduilio',
//         'email' => 'dustyle@styde.net',
//         'password' => '123456',        // dato obligatorio comprobar
//       ])->assertRedirect("/usuarios/{$user->id}");
//       // $this->assertDatabaseHas('users',[   // Comprobacion cuando no se tienen credenciales
//
//         $this->assertCredentials([
//           'name' => 'Duilio',
//           'username' => 'nduilio',
//           'email' => 'dustyle@styde.net',
//           'password' => '123456',
//       ]);
//     }
//     /** @test */
//     function the_name_is_required(){
//       $this->from('usuarios/nuevo')->post('/usuarios/', [
//           'name' => '',
//           'username' => 'nduilio',
//           'email' => 'dustyle@styde.net',
//           'password' => '123456'
//         ])->assertRedirect('usuarios/nuevo')
//         ->assertSessionHasErrors(['name'=>'El campo nombre es obligatorio']);
//
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertEquals(0,User::count());
//     }
//
//
//
//     /** @test */
//     function the_password_is_required(){
//       $this->from('usuarios/nuevo')->post('/usuarios/', [
//           'name' => 'Duilio',
//           'email' => 'Duiol@lot.com',
//           'password' => ''
//         ])->assertRedirect('usuarios/nuevo')
//         ->assertSessionHasErrors(['password'=>'El campo password es obligatorio']);
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertEquals(0,User::count());
//     }
//     /** @test */
//     function the_password_is_short_length(){
//       $this->from('usuarios/nuevo')->post('/usuarios/', [
//           'name' => 'Duilio',
//           'email' => 'Duiol@lot.com',
//           'password' => '123'
//         ])->assertRedirect('usuarios/nuevo')
//         ->assertSessionHasErrors(['password'=>'El password minimo es de 6 caracteres']);
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertEquals(0,User::count());
//     }
//     /** @test */
//     function the_email_is_required(){
//       $this->from('usuarios/nuevo')->post('/usuarios/', [
//           'name' => 'Duilio',
//           'email' => '',
//           'password' => '123456'
//         ])->assertRedirect('usuarios/nuevo')
//         ->assertSessionHasErrors(['email'=>'El campo email es obligatorio']);
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertEquals(0,User::count());
//     }
//     /** @test */
//     function the_email_must_be_valid(){
//
//       $this->from('usuarios/nuevo')
//           ->post('/usuarios/', [
//           'name' => 'Duilio',
//           'email' => 'Email-invalido',
//           'password' => '1234',
//         ])
//         ->assertRedirect('usuarios/nuevo')
//         ->assertSessionHasErrors(['email'=>'El campo email no es valido']);
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertEquals(0,User::count());
//     }
//     /** @test */
//     function the_email_must_be_unique(){
//       // $this->withoutExceptionHandling(); // para verificar cual es el error
//       factory(User::class)->create([
//           'email'=>'duilio@styde.net'
//       ]);
//
//       $this->from('usuarios/nuevo')
//           ->post('/usuarios/', [
//           'name' => 'Duilio',
//           'email' => 'duilio@styde.net',
//           'password' => '1234'
//         ])
//         ->assertRedirect('usuarios/nuevo')
//         ->assertSessionHasErrors(['email'=>'Este correo ya ha sido utilizado']);
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertEquals(1,User::count());
//     }
//
//     /** @test */
//     function it_loads_the_edit_user_page()
//     {
//
//       self::markTestIncomplete();
//       return;
//
//         $this->withoutExceptionHandling(); // para verificar cual es el error
//
//         $user = factory(User::class)->create();
//         $this->get("/usuarios/{$user->id}/editar")
//         ->AssertStatus(200)
//         ->assertViewIs('users.edit')
//         ->assertSee('Editar Usuario')
//         ->assertViewHas('user',function($viewUser) use ($user){
//          return $viewUser->id === $user->id;
//         });
//
//     }
//     /** @test */
//     function the_name_is_required_when_updating_a_user()
//     {
//       // $this->withExceptionHandling();  // para garantizar que se esta utilizando el manejador de excepciones.
//
//       $user = factory(User::class)->create();
//
//       $this->from("usuarios/{$user->id}/editar")
//           ->put("usuarios/{$user->id}", [
//           'name' => '',
//           'email' => 'dustyle@styde.net',
//           'password' => '123456'
//         ])->assertRedirect("usuarios/{$user->id}/editar")
//         ->assertSessionHasErrors(['name']);
//
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertDatabaseMissing('users',['email'=>'dustyle@styde.net']);
//     }
//     /** @test */
//     function the_email_must_be_valid_when_updating_a_user(){
//       // $this->withoutExceptionHandling(); // para verificar cual es el error
//       $user = factory(User::class)->create(['name' => 'Nombre inicial']);
//
//       $this->from("usuarios/{$user->id}/editar")
//           ->put("usuarios/{$user->id}", [
//           'name' => 'Nombre actualizado',
//           'email' => 'correo-no-valido',
//           'password' => '123456'
//         ])->assertRedirect("usuarios/{$user->id}/editar")
//         ->assertSessionHasErrors(['email']);
//
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertDatabaseMissing('users',['name'=>'Nombre Actualizado']);
//     }
//     /** @test */
//     function the_email_must_be_unique_when_updating_a_user(){
//       // marcamos la prueba como incompleta para poderla editar posteriormente.
//       self::markTestIncomplete();
//       return;
//
//       $this->withoutExceptionHandling();
//
//       factory(User::class)->create([
//             'email' => 'existing-email@example.com'
//       ]);
//
//       $user = factory(User::class)->create([
//         'email' => 'duilio@styde.net'
//       ]);
//
//       $this->from("usuarios/{$user->id}/editar")
//           ->put("/usuarios/{$user->id}", [
//           'name' => 'Duilio',
//           'email' => 'existing-email@example.com',
//           'password' => '123456',
//         ])
//         ->assertRedirect("usuarios/{$user->id}/editar")
//         ->assertSessionHasErrors(['email']);
//
//
//     }
//     /** @test */
//     function the_password_is_optional_when_updating_a_user(){
//
//       // $this->withoutExceptionHandling(); // Debemos utilizar el manejador de excepciones.
//
//       $oldPassword = 'CLAVE_ANTERIOR';
//
//       $user = factory(User::class)->create([
//         'password' => bcrypt($oldPassword)
//       ]);
//
//       $this->from("usuarios/{$user->id}/editar")
//           ->put("usuarios/{$user->id}", [
//           'name' => 'Duilio',
//           'username' => 'nduilio',
//           'email' => 'duilio@lot.com',
//           'password' => ''
//         ])
//         ->assertRedirect("usuarios/{$user->id}"); // (users.show)
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertCredentials([
//           'name' => 'Duilio',
//           'username' => 'nduilio',
//           'email' => 'duilio@lot.com',
//           'password' => $oldPassword
//          ]);
//     }
//     /** @test */
//     function the_email_can_stay_the_same_when_updating_a_user()
//     {
//
//       // $this->withoutExceptionHandling(); // Debemos utilizar el manejador de excepciones.
//
//       $user = factory(User::class)->create([
//         'email' => 'duilio@styde.net'
//       ]);
//
//       $this->from("usuarios/{$user->id}/editar")
//           ->put("usuarios/{$user->id}", [
//           'name' => 'Duilio Palacios',
//           'username' => 'nduilio',
//           'email' => 'duilio@styde.net',
//           'password' => '12345678'
//         ])
//         ->assertRedirect("usuarios/{$user->id}"); // (users.show)
//
//         // como estamos utilizando RefreshDatabase, entonces la tabla debe estar vacia en este punto.
//         $this->assertDatabaseHas('users',[
//           'name' => 'Duilio Palacios',
//           'username' => 'nduilio',
//           'email' => 'duilio@styde.net',
//          ]);
//     }
//
// /** @test*/
// function it_deletes_a_user()
//   {
//     // $this->withoutExceptionHandling(); // Debemos utilizar el manejador de excepciones.
//
//       $user = factory(User::class)->create();
//
//       $this->delete("usuarios/{$user->id}")
//       ->assertRedirect('usuarios');
//
//       // Las dos instrucciones siguientes evaluan lo mismo.
//
//       $this->assertDatabaseMissing('users',[
//           'id'=> $user->id
//       ]);
//
//     // $this->assertSame(0,User::count());
//     }
//
//     /** @test */
//     function a_user_can_log_in()
//     {
//         $user = factory(User::class)->create([
//              'username' => 'PruebaLog',
//              'password' => bcrypt('testpass123')
//         ]);
//
//         $this->visit(route('login'))
//             ->type($user->usename, 'username')
//             ->type('testpass123', 'password')
//             ->press('Login')
//             ->see('You are logged in')
//             ->onPage('/dashboard');
//     }

}
