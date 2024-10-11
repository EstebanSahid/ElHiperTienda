<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /* INDEX */

    /* NUEVO USUARIO */
    public function store(Request $request) {
        //dd("hola");
        return Inertia::render('Admin/Users/RegisterUser');
    }
}
