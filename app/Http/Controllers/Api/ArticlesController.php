<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\ResourceObject;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticlesController extends Controller
{


    public function index()
    {


        $articles=Article::applyFilters()->applySorts()->jsonPaginate();

        return ResourceCollection::make($articles);
    }


    public function show(Article $article)
    {

        return ResourceObject::make($article);
    }
}
