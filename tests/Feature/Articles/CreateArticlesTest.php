<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use App\User;
use Laravel\Sanctum\Sanctum;

class CreateArticlesTest extends TestCase
{

    use RefreshDatabase;


    /** @test  */

    public function guest_users_cannot_create_articles()
    {

        $article = array_filter(Article::factory()->raw(['user_id' => null]));

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))->assertStatus(401);

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function authenticates_users_can_create_articles_whithot_permissions()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = Article::factory()->raw();
        //dd($article);

        // $this->assertDatabaseMissing('articles', $article);

        Sanctum::actingAs($user); // auth api token


        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article,
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

        ])->post(route('api.v1.articles.create'))
            // ->dump()
            ->assertStatus(403);
    }

    /** @test  */

    public function authenticates_users_can_create_articles()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = array_filter(Article::factory()->raw([
            'category_id' => null,
            'approved' => true
        ]));
        //dd($article);

        // $this->assertDatabaseMissing('articles', $article);

        Sanctum::actingAs($user,['articles:create']); // auth api token


        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article,
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

        ])->post(route('api.v1.articles.create'))
            // ->dump()
            ->assertCreated();

        // $this->assertDatabaseHas('articles', [
        //     'user_id' => $user->id,
        //     'title' => $article['title'],
        //     'content' => $article['content'],
        //     'slug' => $article['slug'],
        // ]);
    }

    /** @test  */

    public function authenticates_users_cannot_create_articles_on_benhalf_of_another_user()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = array_filter(Article::factory()->raw([
            'category_id' => null,
            'user_id' => null
        ]));
        //dd($article);

        //$this->assertDatabaseMissing('articles', $article);

        Sanctum::actingAs($user,['articles:create']); // auth api token


        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article,
            'relationships' => [
                'categories' => [
                    'data' => [

                        'id' => $category->getRouteKey(),
                        'type' => 'categories'

                    ]

                ],

                'authors' => [
                    'data' => [

                        'id' => User::factory()->create()->getRouteKey(),
                        'type' => 'authors'

                    ]
                ]
            ]

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(403);

        $this->assertDatabaseCount('articles', 0);
    }

    /** @test  */

    public function authors_is_required()
    {
        $article = Article::factory()->raw(['category_id' => 'null']);

        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create()); // auth api token
        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article,
            'relationships' => [
                'categories' => [
                    'data' => [

                        'id' => $category->getRouteKey(),
                        'type' => 'categories'

                    ]
                ]
            ]

        ])->post(route('api.v1.articles.create'))
            //->dump()
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => '/data']]);

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function authors_must_be_a_relationship_object()
    {
        $article = Article::factory()->raw();

        $category = Category::factory()->create();

        $article['authors'] = 'id';

        Sanctum::actingAs(User::factory()->create()); // auth api token
        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article,
            'relationships' => [
                'categories' => [
                    'data' => [

                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]

        ])->post(route('api.v1.articles.create'))
            //->dump()
            ->assertStatus(422)
            ->assertSee('data\/attributes\/authors');

        //$this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function categories_is_required()
    {
        $article = Article::factory()->raw(['category_id' => 'null']);

        Sanctum::actingAs(User::factory()->create()); // auth api token
        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => '/data']]);

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function categories_must_be_a_relationship_object()
    {
        $article = Article::factory()->raw(['category_id' => 'null']);
        //$article['categories'] = 'slug';
        Sanctum::actingAs(User::factory()->create()); // auth api token
        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => '/data']]);

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function title_is_required()
    {
        $article = Article::factory()->raw(['title' => '']);

        Sanctum::actingAs(User::factory()->create()); // auth api token
        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/title');

        $this->assertDatabaseMissing('articles', $article);
    }


    /** @test  */

    public function content_is_required()
    {
        $article = Article::factory()->raw(['content' => '']);
        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/content');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function slug_is_required()
    {
        $article = Article::factory()->raw(['slug' => '']);
        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function slug_must_be_unique()
    {
        Article::factory()->create(['slug' => 'same-slug']);
        $article = Article::factory()->raw(['slug' => 'same-slug']);
        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function slug_must_only_contains_leeters_numbers_and_dashes()
    {

        $article = Article::factory()->raw(['slug' => '#$"#!s']);

        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }


    /** @test  */

    public function slug_must_not_contains_underscores()
    {

        $article = Article::factory()->raw(['slug' => 'hola_mundo']);

        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */
    public function slug_must_not_start_whit_dashes()
    {

        $article = Article::factory()->raw(['slug' => '-hola-mundo']);

        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */
    public function slug_must_not_end_whit_dashes()
    {

        $article = Article::factory()->raw(['slug' => 'hola-mundo-']);

        Sanctum::actingAs(User::factory()->create()); // auth api token

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' => $article

        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))

            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }
}
