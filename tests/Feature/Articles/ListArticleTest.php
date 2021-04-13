<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListArticleTest extends TestCase
{

    use RefreshDatabase;

    /** @test  */

    public function can_fetch_single_article()
    {

        $this->withoutExceptionHandling();
        $article =factory(Article::class)->create();

        $response=$this->getJson('api/v1/articles/'.$article->getRouteKey());

        $response->assertSee($article->title);

    }
}
