<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function can_login_whith_valid_credentials()
    {

        $user =User::factory()->create();

        $this->postJson(route('api.v1.login'),[
            'email'=> $user->email,
            'password'=> 'password',
            'divice_name'=> 'iPhone de '.$user->name
        ])->dump();



    }
}
