<?php

namespace Tests\Unit\Commands;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneratePermisionsTest extends TestCase
{
    use RefreshDatabase;


    /** @test  */

    public function can_geberate_permissions_for_registered_api_resources()
    {

        config([
            'json-api-v1.resources'=>[
                'articles' => \App\Models\Article::class,
            ]
        ]);


        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

            //dd(Permission::pluck('name')->toArray());

            $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

            $this->assertDatabaseCount('permissions', count(Permission::$abilities));
    }
}
