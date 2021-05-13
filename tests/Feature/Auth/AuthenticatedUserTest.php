<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticatedUserTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function can_fecth_the_authenticated_user()
    {
        $user= User::factory()->create();

        Sanctum::actingAs($user);
        $this->getJson(route('api.v1.user'))
        ->assertJson([
            'email' => $user->email
        ]);


    }

    /** @test  */

    public function guest_cannot_fectch_any_user()
    {
        $this->getJson(route('api.v1.user'))
        ->assertStatus(401);

    }
}
