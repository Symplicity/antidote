<?php

namespace app\Http\Controllers;

use App\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }
}
