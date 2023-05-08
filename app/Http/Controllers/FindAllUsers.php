<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FindAllUsers extends Controller
{
    public function index()
    {
        return User::all()->get();
    }
}
