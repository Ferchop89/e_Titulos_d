<?php

Route::get('/home', 'Dashboard@index')
        ->name('admin_dashboard');

Route::get('/usuarios',[
    'uses' => 'Dashboard@usuarios',
    'as' => 'admin/usuarios',
    'roles' => ['Admin']
]);//->name('users');

Route::get('/usuarios/{user}',[
    'uses' => 'Dashboard@ver_usuario',
    'roles' => ['Admin']
    ])
      ->where('user','[0-9]+')
      ->name('ver_usuario');

Route::get('/usuarios/{user}/editar', [
    'uses' => 'Dashboard@editar_usuario',
    'as' => 'admin.users.editar_usuarios',
    'roles' => ['Admin']
    ])->where('user','[0-9]+');

Route::get('/usuarios/nuevo', [
    'uses' => 'Dashboard@crear_usuario',
    'as' => 'admin/usuarios/nuevo',
    'roles' => ['Admin']
    ]);

Route::post('/usuarios','Dashboard@store');
//Ruta para ver cambios realizados
Route::put('/usuarios/{user}','Dashboard@update');

// Route::get('/usuarios/nuevo', 'Dashboard@crear_usuario')
//         ->name('crear_usuario');

Route::delete('/usuarios/{user}' ,'Dashboard@eliminar_usuario')
        ->name('eliminar_usuario');
Route::any('{any}', function (){
    return response()->view('errors/404', [], 404);
})->where('any', '.*');
