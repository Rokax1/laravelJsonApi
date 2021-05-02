<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateCategories extends TestCase
{

    use RefreshDatabase;

   /** @test  */

   public function guest_users_cannot_create_categories()
   {

       $category = Category::factory()->raw();

       $this->jsonApi()->withData([

           'type' => 'categories',
           'attributes' => $category

       ])->post(route('api.v1.categories.create'))->assertStatus(401);

       $this->assertDatabaseMissing('categories', $category);
   }

   /** @test  */

   public function authenticates_users_can_create_categories()
   {
       $user = User::factory()->create();

       $category = Category::factory()->raw();

       $this->assertDatabaseMissing('categories', $category);


       Sanctum::actingAs($user); // auth api token

       $this->assertDatabaseMissing('categories', $category);
       $this->jsonApi()->withData([

           'type' => 'categories',
           'attributes' => $category

       ])->post(route('api.v1.categories.create'))->assertCreated();

       $this->assertDatabaseHas('categories', [
           'slug' => $category['slug'],
           'name' => $category['name'],
       ]);
   }

    /** @test  */

    public function name_is_required()
    {
        $category = Category::factory()->raw(['name' => '']);

        Sanctum::actingAs(User::factory()->create()); // auth api token
        $this->jsonApi()->withData([

            'type' => 'categories',
            'attributes' => $category

        ])->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/name');

        $this->assertDatabaseMissing('categories', $category);
    }


     /** @test  */

     public function slug_is_required()
     {
         $category = Category::factory()->raw(['slug' => '']);

         Sanctum::actingAs(User::factory()->create()); // auth api token
         $this->jsonApi()->withData([

             'type' => 'categories',
             'attributes' => $category

         ])->post(route('api.v1.categories.create'))
             ->assertStatus(422)
             ->assertSee('/data\/attributes\/slug');

         $this->assertDatabaseMissing('categories', $category);
     }


     /** @test  */

     public function slug_must_be_unique()
     {
         Category::factory()->create(['slug' => 'same-slug']);
         $category = Category::factory()->raw(['slug' => 'same-slug']);
         Sanctum::actingAs(User::factory()->create()); // auth api token

         $this->jsonApi()->withData([

             'type' => 'categories',
             'attributes' => $category

         ])->post(route('api.v1.categories.create'))
             ->assertStatus(422)
             ->assertSee('/data\/attributes\/slug');

         $this->assertDatabaseMissing('categories', $category);
     }

      /** @test  */

    public function slug_must_only_contains_leeters_numbers_and_dashes()
    {

        $category = Category::factory()->raw(['slug' => '#$"#!s']);

        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'categories',
            'attributes' => $category

        ])->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

      /** @test  */

      public function slug_must_not_contains_underscores()
      {

          $category = Category::factory()->raw(['slug' => 'hola_mundo']);

          Sanctum::actingAs(User::factory()->create()); // auth api token

          $this->jsonApi()->withData([

              'type' => 'categories',
              'attributes' => $category

          ])->post(route('api.v1.categories.create'))
              ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']))
              ->assertStatus(422)
              ->assertSee('/data\/attributes\/slug');

          $this->assertDatabaseMissing('categories', $category);
      }

         /** @test  */
    public function slug_must_not_start_whit_dashes()
    {

        $category = Category::factory()->raw(['slug' => '-hola-mundo']);

        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'categories',
            'attributes' => $category

        ])->post(route('api.v1.categories.create'))
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test  */
    public function slug_must_not_end_whit_dashes()
    {

        $category = Category::factory()->raw(['slug' => 'hola-mundo-']);

        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'categories',
            'attributes' => $category

        ])->post(route('api.v1.categories.create'))
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))

            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

}
