<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth:student');
  }
  /**
   * show dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      return view('student');
  }
}
