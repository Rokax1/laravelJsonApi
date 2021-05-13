<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function can_register()
    {

        $response = $this->postJson(route('api.v1.register'), [
            'name' => 'leandro sepulveda',
            'email' => 'xd@gmail.com',
            'divice_name' => 'dispositivo de leandro',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'the plain text token id invalid'
        );

        $this->assertDatabaseHas('users', [
            'name' => 'leandro sepulveda',
            'email' => 'xd@gmail.com',
        ]);
    }

    /** @test  */

    public function cannot_register_twice()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.register'))->assertStatus(204);
    }

    /** @test  */
    public function name_is_required()
    {

        $this->postJson(route('api.v1.register'), [
            'name' => '',
            'email' => 'xd@gmail.com',
            'divice_name' => 'dispositivo de leandro',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('name');
    }

    /** @test  */
    public function email_is_required()
    {

        $this->postJson(route('api.v1.register'), [
            'name' => 'leandro',
            'email' => '',
            'divice_name' => 'dispositivo de leandro',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('email');
    }

    /** @test  */
    public function email_is_valid()
    {

        $this->postJson(route('api.v1.register'), [
            'name' => 'leandro',
            'email' => 'invalid-email',
            'divice_name' => 'dispositivo de leandro',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('email');
    }

    /** @test  */
    public function email_must_be_unique()
    {

        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'), [
            'name' => $user->email,
            'email' => 'invalid-email',
            'divice_name' => 'dispositivo de leandro',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('email');
    }

    /** @test  */
    public function password_is_required()
    {

        $this->postJson(route('api.v1.register'), [
            'name' => 'leandro',
            'email' => 'invalid-email',
            'divice_name' => 'dispositivo de leandro',
            'password' => '',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('password');
    }

    /** @test  */
    public function password_must_be_confirmed()
    {

        $this->postJson(route('api.v1.register'), [
            'name' => 'leandro',
            'email' => 'roka@gmail.com',
            'divice_name' => 'dispositivo de leandro',
            'password' => 'password',
            'password_confirmation' => 'password-no-confirmed'
        ])->assertJsonValidationErrors('password');
    }

    /** @test  */
    public function divice_name_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => 'leandro',
            'email' => 'roka@gmail.com',
            'divice_name' => '',
            'password' => 'password',
        ])->assertJsonValidationErrors('divice_name');
    }
}
