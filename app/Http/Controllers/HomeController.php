<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {

        return view('home', ['user' => Auth::user()]);
    }
}
