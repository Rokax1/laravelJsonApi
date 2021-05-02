<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateCategories extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function guest_users_cannot_update_category()
    {

        $category = Category::factory()->create();


        $this->jsonApi()->patch(route('api.v1.categories.update', $category))
            ->assertStatus(401);
    }

     /** @test  */

     public function authenticated_users_can_update_their_articles()
     {

         $category = Category::factory()->create();

         Sanctum::actingAs(User::factory()->create());

         $this->jsonApi()
             ->withData([

                     'type' => 'categories',
                     'id' => $category->getRouteKey(),
                     'attribute' => [
                         'name' => 'name-change',
                         'slug' => 'name-change',

                     ]
             ])
             ->patch(route('api.v1.categories.update', $category))
             ->assertStatus(200);

        //  $this->assertDatabaseHas('categories', [
        //      'name' => 'name-change',
        //      'slug' => 'name-change',

        //  ]);
     }

     /** @test  */
    public function can_update_the_name_only()
    {

        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        //dd($article->getRouteKey());
        $this->jsonApi()
            ->withData([

                    'type' => 'categories',
                    'id' => $category->getRouteKey(),
                    'attribute' => [
                        'title' => 'Title change',

                    ]

            ])
            ->patch(route('api.v1.categories.update', $category))
            ->assertStatus(200);

        // $this->assertDatabaseHas('articles', [
        //     'title' => 'Title change',

        // ]);
    }

    /** @test  */
    public function can_update_the_slug_only()
    {

        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->withData([

                    'type' => 'categories',
                    'id' => $category->getRouteKey(),
                    'attribute' => [
                        'slug' => 'slug-change',

                    ]

            ])
            ->patch(route('api.v1.categories.update', $category))
            ->assertStatus(200);

        // $this->assertDatabaseHas('articles', [
        //     'title' => 'slug change',

        // ]);
    }
}
