<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\User;
use Laravel\Sanctum\Sanctum;

class CreateArticlesTest extends TestCase
{

    use RefreshDatabase;


    /** @test  */

    public function guest_users_cannot_create_articles()
    {

        $article = array_filter(factory(Article::class)->raw(['user_id' => null]));

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))->assertStatus(401);

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function authenticates_users_can_create_articles()
    {
        $user = factory(User::class)->create();
        $article = array_filter(factory(Article::class)->raw(['user_id' => null]));
        $this->assertDatabaseMissing('articles', $article);

        //dump($article);
        Sanctum::actingAs($user); // auth api token

        $this->assertDatabaseMissing('articles', $article);
        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))->assertCreated();

        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'title' => $article['title'],
            'content' => $article['content'],
            'slug' => $article['slug'],
        ]);


    }

    /** @test  */

    public function title_is_required()
    {
        $article = factory(Article::class)->raw(['title' => '']);

        Sanctum::actingAs(factory(User::class)->create()); // auth api token
        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/title');

        $this->assertDatabaseMissing('articles', $article);
    }


    /** @test  */

    public function content_is_required()
    {
        $article = factory(Article::class)->raw(['content' => '']);
        Sanctum::actingAs(factory(User::class)->create()); // auth api token

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/content');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function slug_is_required()
    {
        $article = factory(Article::class)->raw(['slug' => '']);
        Sanctum::actingAs(factory(User::class)->create()); // auth api token

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function slug_must_be_unique()
    {
        factory(Article::class)->create(['slug' => 'same-slug']);
        $article = factory(Article::class)->raw(['slug' => 'same-slug']);
        Sanctum::actingAs(factory(User::class)->create()); // auth api token

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */

    public function slug_must_only_contains_leeters_numbers_and_dashes()
    {

        $article = factory(Article::class)->raw(['slug' => '#$"#!s']);

        Sanctum::actingAs(factory(User::class)->create()); // auth api token

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }


    /** @test  */

    public function slug_must_not_contains_underscores()
    {

        $article = factory(Article::class)->raw(['slug' => 'hola_mundo']);

        Sanctum::actingAs(factory(User::class)->create()); // auth api token

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_underscores',['attribute'=>'slug']))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */
    public function slug_must_not_start_whit_dashes()
    {

        $article = factory(Article::class)->raw(['slug' => '-hola-mundo']);

        Sanctum::actingAs(factory(User::class)->create()); // auth api token

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
        ->assertSee(trans('validation.no_starting_dashes',['attribute'=>'slug']))
            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test  */
    public function slug_must_not_end_whit_dashes()
    {

        $article = factory(Article::class)->raw(['slug' => 'hola-mundo-']);

        Sanctum::actingAs(factory(User::class)->create()); // auth api token

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' => $article
            ]
        ])->post(route('api.v1.articles.create'))
        ->assertSee(trans('validation.no_ending_dashes',['attribute'=>'slug']))

            ->assertStatus(422)
            ->assertSee('/data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }
}
