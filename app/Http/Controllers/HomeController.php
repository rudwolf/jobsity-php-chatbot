<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $encrypted = Crypt::encryptString(Auth::user()->email.'::'.Auth::user()->password);
        $cookie = cookie('login_hash', $encrypted, 60);
        return redirect()->to( url( '/' ) )->withCookie($cookie);
    }
}
