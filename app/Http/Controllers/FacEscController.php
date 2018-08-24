<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FacEscController extends Controller
{
    
    public function index()
    {
        return view('/menus/consulta_re');
    }

    public function store(Request $request)
    {
    	
    	$request->validate([
          'num_cuenta' => 'required|numeric|digits:9'
          ],[
           'num_cuenta.required' => 'El campo es obligatorio',
           'num_cuenta.numeric' => 'El campo debe contener solo números',
           'num_cuenta.digits'  => 'El campo debe ser de 9 dígitos',
      ]);

    	return redirect()->route('login');
    }

}
