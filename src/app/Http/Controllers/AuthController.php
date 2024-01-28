<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create ()
    {
        $name = Auth::user()->name;
        return view('index', compact('name'));
    }

    public function index()
    {
        return view('attendance');

    }
}
