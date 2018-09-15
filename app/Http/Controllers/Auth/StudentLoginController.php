<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Route;

class StudentLoginController extends Controller
{
  public function __construct()
  {
    $this->middleware('guest:student', ['except' => ['logout']]);
  }

  public function showLoginForm()
  {
    return view('auth.student_login');
  }

  public function login(Request $request)
  {
    // Validate the form data
    $this->validate($request, [
      'num_cta'   => 'required|numeric|digits:9',
      'password' => 'required|numeric|digits:8'
    ]);

    // Attempt to log the user in
    if (Auth::guard('student')->attempt(['num_cta' => $request->num_cta, 'password' => $request->password], $request->remember)) {
      // if successful, then redirect to their intended location
      return redirect()->intended(route('ati'));
    }
    // if unsuccessful, then redirect back to the login with the form data
    return redirect()->back()->withInput($request->only('num_cta', 'remember'));
  }

  public function logout()
  {
      Auth::guard('student')->logout();
      return redirect('/');
  }
}
