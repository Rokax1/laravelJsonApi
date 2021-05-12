<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request){

      $user=  User::where('email', $request->email)->first();

      $user->createToken($request->divice_name);
    }
}
