<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // permisos de admin [articles:admin]

    // public function before(User $user){

    //     if ($user->tokenCan('articles:admin')) {
    //        return true;
    //     }

    // }


    public function create(User $user ,$request){



            return $user->tokenCan('articles:create') && $user->id === $request->json('data.relationships.authors.data.id');


        return false;
    }

    public function update(User $user , $article){

        return $user->tokenCan('articles:update') && $article->user->is($user);

    }

    public function delete(User $user , $article){

        return $user->tokenCan('articles:delete') && $article->user->is($user);

    }

    public function modifyCategories(User $user ,$article){

        return $user->tokenCan('articles:modify-categories') && $article->user->is($user);


    }
    public function modifyAuthors(User $user ,$article){

        return $user->tokenCan('articles:modify-authors') && $article->user->is($user);


    }
}
