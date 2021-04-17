<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(1),
        'slug' => $faker->slug,
        'content' => $faker->sentence(1),
        'category_id' => factory(App\Models\Category::class),
        'user_id' => factory(App\User::class),
    ];
});
