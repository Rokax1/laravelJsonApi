<?php

namespace App\Http\Responses;

use App\User;
use Illuminate\Contracts\Support\Responsable;

class TokenReponse implements Responsable
{
    private $user;

    public function __construct(User $user )
    {
        $this->user=$user;
    }

    public function toResponse($request)
    {
        return response()->json([
            'plain-text-token' => $this->user->createToken($request->divice_name)->plainTextToken
        ]);
    }

}
