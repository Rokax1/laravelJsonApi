<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function can_login_whith_valid_credentials()
    {

        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'divice_name' => 'iPhone de ' . $user->name
        ]);
        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'the plain text token id invalid'
        );
    }

    /** @test  */
    public function cannot_login_twice()
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.v1.login'))
            ->assertStatus(204);

    }

    /** @test  */

    function cannot_login_whith_invalid_credentials()
    {

        $this->postJson(route('api.v1.login'), [
            'email' => 'bad.user@gmail.com',
            'password' => 'wrong-password',
            'divice_name' => 'iPhone de juanito'
        ])->assertJsonValidationErrors('email');
    }



    /** @test  */
    public function email_is_required()
    {

        $this->postJson(route('api.v1.login'), [
            'email' => '',
            'password' => 'wrong-password',
            'divice_name' => 'iPhone de juanito'
        ])->assertSee(__('validation.required', ['attribute' => 'email']))
            ->assertJsonValidationErrors('email');
    }

    /** @test  */
    public function email_is_valid()
    {

        $this->postJson(route('api.v1.login'), [
            'email' => 'invali-email',
            'password' => 'wrong-password',
            'divice_name' => 'iPhone de juanito'
        ])->assertSee(__('validation.email', ['attribute' => 'email']))
            ->assertJsonValidationErrors('email');
    }


    /** @test  */
    public function password_is_required()
    {

        $this->postJson(route('api.v1.login'), [
            'email' => 'xd@gmail.com',
            'password' => '',
            'divice_name' => 'iPhone de juanito'
        ])->assertJsonValidationErrors('password');
    }

    /** @test  */
    public function divice_name_is_required()
    {

        $this->postJson(route('api.v1.login'), [
            'email' => 'xd@gmail.com',
            'password' => 'password',
            'divice_name' => ''
        ])->assertJsonValidationErrors('divice_name');
    }
}
