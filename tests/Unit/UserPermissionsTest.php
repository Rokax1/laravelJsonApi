<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class UserPermissionsTest extends TestCase
{
    use RefreshDatabase;
    /** @test  */

    function can_assing_permission_to_a_user(){

        $user=User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);

        $this->assertCount(1,$user->fresh()->permissions);

    }

     /** @test  */

     function cannot_assing_the_same_permission_twice(){

        $user=User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);
        $user->givePermissionTo($permission);

        $this->assertCount(1,$user->fresh()->permissions);

    }
}
