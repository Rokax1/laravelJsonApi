<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteCategories extends TestCase
{

    use RefreshDatabase;

  /** @test  */

  public function guest_user_canot_delete_categories()
  {

      $category = Category::factory()->create();

      $this->jsonApi()->delete(route('api.v1.categories.delete',$category))
      ->assertStatus(401);

  }

  /** @test  */

  public function guest_user_can_delete_their_categories()
  {

      $category = Category::factory()->create();

      Sanctum::actingAs(User::factory()->create());

      $this->jsonApi()->delete(route('api.v1.categories.delete',$category))
      ->assertStatus(204);

  }
}
