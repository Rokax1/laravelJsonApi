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
        factory(Article::class)->create([
            'title' => 'aprende laravel desde 0'
        ]);
        factory(Article::class)->create([
            'title' => 'other article'
        ]);

        $url = route('api.v1.articles.index', ['filter[title]' => 'laravel']);

        // DB::listen(function ($query) {
        //     dump($query->sql);
        //     // $query->bindings;
        //     // $query->time;
        // });
        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('aprende laravel desde 0')
            ->assertDontSee('other article');
    }

    /** @test  */

    public function can_filter_articles_by_content()
    {
        factory(Article::class)->create([
            'content' => '<div> aprende laravel desde 0 </div>'
        ]);
        factory(Article::class)->create([
            'content' => '<div> other article </div>'
        ]);

        $url = route('api.v1.articles.index', ['filter[content]' => 'laravel']);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('aprende laravel desde 0')
            ->assertDontSee('other article');
    }

    /** @test  */

    public function can_filter_articles_by_year()
    {
        factory(Article::class)->create([
            'title' => 'Article from 2020',
            'created_at' => now()->year(2020)
        ]);
        factory(Article::class)->create([
            'title' => 'Article from 2021',
            'created_at' => now()->year(2021)
        ]);

        $url = route('api.v1.articles.index', ['filter[year]' => 2020]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2020')
            ->assertDontSee('Article from 2021');
    }

    /** @test  */

    public function can_filter_articles_by_month()
    {
        factory(Article::class)->create([
            'title' => 'Article from February',
            'created_at' => now()->month(2)
        ]);
        factory(Article::class)->create([
            'title' => 'Another Article from February',
            'created_at' => now()->month(2)
        ]);
        factory(Article::class)->create([
            'title' => 'Article from Junary',
            'created_at' => now()->month(1)
        ]);


        $url = route('api.v1.articles.index', ['filter[month]' => 2]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from February')
            ->assertDontSee('Article from Junary');
    }

    /** @test  */
    public function cannot_filter_articles_by_unkunow_filters()
    {
        factory(Article::class)->create();



        $url = route('api.v1.articles.index', ['filter[unkunow]' => 2]);

        $this->getJson($url)->assertStatus(400)->dump();
    }

    /** @test  */
    public function can_search_articles_by_title_and_content()
    {
        factory(Article::class)->create([
            'title' => 'Article from Aprendible',
            'content' => 'content'
        ]);
        factory(Article::class)->create([
            'title' => 'Another Article ',
            'content' => 'Content Aprendible...'
        ]);
        factory(Article::class)->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);


        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible']);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Content Aprendible...')
            ->assertDontSee('Title 2');
    }


    /** @test  */

    public function can_search_articles_by_title_and_content_whit_multiple_terms()
    {
        factory(Article::class)->create([
            'title' => 'Article from Aprendible',
            'content' => 'content'
        ]);
        factory(Article::class)->create([
            'title' => 'Another Article ',
            'content' => 'Content Aprendible...'
        ]);
        factory(Article::class)->create([
            'title' => 'Another Laravel Article',
            'content' => 'Content...'
        ]);
        factory(Article::class)->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);


        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible Laravel']);

        $this->getJson($url)
            ->assertJsonCount(3, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Content Aprendible...')
            ->assertSee('Another Laravel Article')
            ->assertDontSee('Title 2');
    }
}
