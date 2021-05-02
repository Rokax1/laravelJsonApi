<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function can_filter_articles_by_title()
    {
        Article::factory()->create([
            'title' => 'aprende laravel desde 0'
        ]);
        Article::factory()->create([
            'title' => 'other article'
        ]);

        $url = route('api.v1.articles.index', ['filter[title]' => 'laravel']);

        // DB::listen(function ($query) {
        //     dump($query->sql);
        //     // $query->bindings;
        //     // $query->time;
        // });
        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('aprende laravel desde 0')
            ->assertDontSee('other article');
    }

    /** @test  */

    public function can_filter_articles_by_content()
    {
        Article::factory()->create([
            'content' => '<div> aprende laravel desde 0 </div>'
        ]);
        Article::factory()->create([
            'content' => '<div> other article </div>'
        ]);

        $url = route('api.v1.articles.index', ['filter[content]' => 'laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('aprende laravel desde 0')
            ->assertDontSee('other article');
    }

    /** @test  */

    public function can_filter_articles_by_year()
    {
        Article::factory()->create([
            'title' => 'Article from 2020',
            'created_at' => now()->year(2020)
        ]);
        Article::factory()->create([
            'title' => 'Article from 2021',
            'created_at' => now()->year(2021)
        ]);

        $url = route('api.v1.articles.index', ['filter[year]' => 2020]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2020')
            ->assertDontSee('Article from 2021');
    }

    /** @test  */

    public function can_filter_articles_by_month()
    {
        Article::factory()->create([
            'title' => 'Article from February',
            'created_at' => now()->month(3)
        ]);
        Article::factory()->create([
            'title' => 'Another Article from February',
            'created_at' => now()->month(3)
        ]);
        Article::factory()->create([
            'title' => 'Article from Junary',
            'created_at' => now()->month(1)
        ]);


        $url = route('api.v1.articles.index', ['filter[month]' => 3]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from February')
            ->assertDontSee('Article from Junary');
    }

    /** @test  */
    public function cannot_filter_articles_by_unkunow_filters()
    {
        Article::factory()->create();



        $url = route('api.v1.articles.index', ['filter[unkunow]' => 2]);

        $this->jsonApi()->get($url)->assertStatus(400);
    }

    /** @test  */
    public function can_search_articles_by_title_and_content()
    {
        Article::factory()->create([
            'title' => 'Article from Aprendible',
            'content' => 'content'
        ]);
        Article::factory()->create([
            'title' => 'Another Article ',
            'content' => 'Content Aprendible...'
        ]);
        Article::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);


        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Content Aprendible...')
            ->assertDontSee('Title 2');
    }


    /** @test  */

    public function can_search_articles_by_title_and_content_whit_multiple_terms()
    {
        Article::factory()->create([
            'title' => 'Article from Aprendible',
            'content' => 'content'
        ]);
        Article::factory()->create([
            'title' => 'Another Article ',
            'content' => 'Content Aprendible...'
        ]);
        Article::factory()->create([
            'title' => 'Another Laravel Article',
            'content' => 'Content...'
        ]);
        Article::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);


        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(3, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Content Aprendible...')
            ->assertSee('Another Laravel Article')
            ->assertDontSee('Title 2');
    }
}
