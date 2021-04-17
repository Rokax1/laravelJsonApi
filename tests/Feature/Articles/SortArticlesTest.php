<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class SortArticlesTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function it_can_sort_articles_by_title_asc()
    {

        factory(Article::class)->create(['title' => 'C Title']);
        factory(Article::class)->create(['title' => 'A Title']);
        factory(Article::class)->create(['title' => 'B Title']);

        $url = route('api.v1.articles.index', ["sort" => "title"]);

        //dd($url);

        $this->getJson($url)->assertSeeInOrder([
            'A Title',
            'B Title',
            'C Title',
        ]);

    }

      /** @test  */
    public function it_can_sort_articles_by_title_desc()
    {
        factory(Article::class)->create(['title' => 'C Title']);
        factory(Article::class)->create(['title' => 'A Title']);
        factory(Article::class)->create(['title' => 'B Title']);

        $url = route('api.v1.articles.index', ['sort' => '-title']);



        $this->getJson($url)->assertSeeInOrder([
            'C Title',
            'B Title',
            'A Title',
        ]);
    }

/** @test  */
    public function it_can_sort_articles_by_title_and_content()
    {
        factory(Article::class)->create([
            'title' => 'C Title',
            'content' => 'B content'

            ]);
        factory(Article::class)->create([

            'title' => 'A Title',
            'content' => 'C content'

            ]);
        factory(Article::class)->create([

            'title' => 'B Title',
            'content' => 'D content'

            ]);





        $url = route('api.v1.articles.index').'?sort=title,-content';

        //dd($url);


        $this->getJson($url)->assertSeeInOrder([
            'A Title',
            'B Title',
            'C Title',
        ]);

        $url = route('api.v1.articles.index').'?sort=-content,title';

        $this->getJson($url)->assertSeeInOrder([
            'D content',
            'C content',
            'B content',
        ]);



    }

/** @test  */
    public function it_can_sort_articles_by_unkunown_fields()
    {
        factory(Article::class)->times(3)->create();


        $url = route('api.v1.articles.index').'?sort=unknwn';



        $this->getJson($url)->assertStatus(400);
    }


}
