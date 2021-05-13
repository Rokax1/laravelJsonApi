<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\TokenReponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {

        $request->validate([
            'name'=> ['required'],
            'email'=>['required','email','unique:users'],
            'password'=>['required','confirmed'],
            'divice_name'=>['required'],
        ]);

      $user= User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=>Hash::make($request->password),

        ]);

        return new TokenReponse($user);

        // return response()->json([
        //     'plain-text-token' => $user->createToken($request->divice_name)->plainTextToken
        // ]);
    }
}
