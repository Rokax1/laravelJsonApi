<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
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

        $article = Article::factory()->create();


        $this->jsonApi()->patch(route('api.v1.articles.update', $article))
            ->assertStatus(401);
    }


    /** @test  */

    public function authenticated_users_can_update_their_articles()
    {

        $article = Article::factory()->create();
        $category = Category::factory()->create();
        Sanctum::actingAs($user = $article->user, ['articles:update']);

        $this->jsonApi()
            ->withData([
                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attribute' => [
                    'title' => 'Title-change',
                    'slug' => 'Title-change',
                    'content' => 'Content-Change',
                ],
                'relationships' => [
                    'categories' => [
                        'data' => [

                            'id' => $category->getRouteKey(),
                            'type' => 'categories'

                        ]

                    ],

                    'authors' => [
                        'data' => [

                            'id' => $user->getRouteKey(),
                            'type' => 'authors'

                        ]
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

    public function authenticated_users_cannot_update_their_articles_without_permissions()
    {

        $article = Article::factory()->create();
        $category = Category::factory()->create();
        Sanctum::actingAs($user = $article->user);

        $this->jsonApi()
            ->withData([
                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attribute' => [
                    'title' => 'Title-change',
                    'slug' => 'Title-change',
                    'content' => 'Content-Change',
                ],
                'relationships' => [
                    'categories' => [
                        'data' => [

                            'id' => $category->getRouteKey(),
                            'type' => 'categories'

                        ]

                    ],

                    'authors' => [
                        'data' => [

                            'id' => $user->getRouteKey(),
                            'type' => 'authors'

                        ]
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(403);
    }

    /** @test  */

    public function authenticated_users_cannot_update_others_articles()
    {

        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

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

        $article = Article::factory()->create();

        Sanctum::actingAs($user = $article->user, ['articles:update']);

        //dd($article->getRouteKey());
        $this->jsonApi()
            ->withData([

                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attribute' => [
                    'title' => 'Title change',

                ],


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

        $article = Article::factory()->create();

        Sanctum::actingAs($article->user, ['articles:update']);

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


    /** @test  */
    public function can_replace_the_categories()
    {

        $article = Article::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($article->user,['articles:modify-categories']);

        $this->jsonApi()
            ->withData([
                    'type' => 'categories',
                    'id' => $category->getRouteKey(),
            ])
            ->patch(route('api.v1.articles.relationships.categories.replace', $article))
            ->assertStatus(204);

        $this->assertDatabaseHas('articles', [
            'category_id' => $category->id,

        ]);
    }

       /** @test  */
       public function can_replace_the_author()
       {

           $article = Article::factory()->create();
           $author = User::factory()->create();

           Sanctum::actingAs($article->user,['articles:modify-authors']);

           $this->jsonApi()
               ->withData([
                       'type' => 'authors',
                       'id' => $author->getRouteKey(),
               ])
               ->patch(route('api.v1.articles.relationships.authors.replace', $article))
               ->assertStatus(204);

           $this->assertDatabaseHas('articles', [
               'user_id' => $author->id,

           ]);
       }
}
