<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'divice_name' => ['required'],
        ]);

        $user =  User::where('email', $request->email)->first();


        if (!Hash::check($request->password, optional($user)->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ]);
        }

        return response()->json([
            'plain-text-token' => $user->createToken($request->divice_name)->plainTextToken
        ]);
    }


    public function logout(Request $request)
    {

       // return $request->user()->tokens()->delete(); // borrar todos los tokens si es que se necesita un solo token por usuario valido
       $request->user()->currentAccessToken()->delete();

       return response()->noContent();

    }
}
