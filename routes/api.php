<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use CloudCreativity\LaravelJsonApi\Facades\JsonApi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Route::get('articles','ArticlesController@index')->name('api.v1.articles.index');

// Route::get('articles/{article}','ArticlesController@show')->name('api.v1.articles.show');

JsonApi::register('v1')->routes(function($api){

    // a nivel de rutas solo se puede ocipar hasOne y hasMany si se necesita otra ocupar adapter
    $api->resource('articles')->relationships(function($api){
        $api->hasOne('authors')->except('replace');
        $api->hasOne('categories')->except('replace');
    });

    $api->resource('authors')->only('index','read')->relationships(function($api){
        $api->hasMany('articles')->except('replace','add','remove');
    });


    $api->resource('categories')->relationships(function($api){
        $api->hasMany('articles')->except('replace','add','remove');
    });

    //->only('read','index','create','update','delete');
    // $api->resource('articles')->only('create','update','delete')->middleware('auth');
    // $api->resource('articles')->except('create','update','delete');
});


