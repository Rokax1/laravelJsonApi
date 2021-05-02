<?php

namespace Tests\Feature\Authors;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

use function PHPUnit\Framework\assertTrue;

class ListAuthorsTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function can_fetch_all_authors()
    {
        $authors= User::factory()->times(3)->create();

        $this->jsonApi()->get(route('api.v1.authors.index'))->assertSee($authors[0]->name);

    }

     /** @test  */

     public function can_fetch_single_authors()
     {
         $author= User::factory()->create();

         $response = $this->jsonApi()->get(route('api.v1.authors.read',$author))
            ->assertSee($author->name);


            assertTrue(
                Str::isUuid($response->json('data.id'))


            , 'the authors id must be uuid');
     }
}
