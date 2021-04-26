<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function guest_users_cannot_update_articles()
    {

        $article = factory(Article::class)->create();


        $this->jsonApi()->patch(route('api.v1.articles.update', $article))
            ->assertStatus(401);
    }


    /** @test  */

    public function authenticated_users_can_update_their_articles()
    {

        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attribute' => [
                        'title' => 'Title-change',
                        'slug' => 'Title-change',
                        'content' => 'Content-Change',
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);

        // $this->assertDatabaseHas('articles', [
        //     'title' => 'Title-change',
        //     'slug' => 'Title-change',
        //     'content' => 'Content-Change',
        // ]);
    }

      /** @test  */

      public function authenticated_users_cannot_update_others_articles()
      {

          $article = factory(Article::class)->create();

          Sanctum::actingAs(factory(User::class)->create());

          $this->jsonApi()
              ->content([
                  'data' => [
                      'type' => 'articles',
                      'id' => $article->getRouteKey(),
                      'attribute' => [
                          'title' => 'Title-change',
                          'slug' => 'Title-change',
                          'content' => 'Content-Change',
                      ]
                  ]
              ])
              ->patch(route('api.v1.articles.update', $article))
              ->assertStatus(403);

          $this->assertDatabaseMissing('articles', [
              'title' => 'Title-change',
              'slug' => 'Title-change',
              'content' => 'Content-Change',
          ]);
      }

    /** @test  */
    public function can_update_the_titile_only()
    {

        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);

        //dd($article->getRouteKey());
        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attribute' => [
                        'title' => 'Title change',

                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);

        // $this->assertDatabaseHas('articles', [
        //     'title' => 'Title change',

        // ]);
    }

    /** @test  */
    public function can_update_the_slug_only()
    {

        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attribute' => [
                        'slug' => 'slug-change',

                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);

        // $this->assertDatabaseHas('articles', [
        //     'title' => 'slug change',

        // ]);
    }
}
