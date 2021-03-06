<?php

namespace App\Models;

use App\Models\Traits\HasSorts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $allowedSorts = ['title', 'content'];

    public $type = 'articles';





    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'category_id' => 'integer',
        'user_id' => 'string',
    ];


    public function getRouteKeyName()
    {
        return 'slug';
    }



    public function fields()
    {

        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,

        ];
    }




    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }


    // Scopes


    public function scopeTitle(Builder $query, $value)
    {

        return  $query->where('title', 'like', "% $value %");
    }

    public function scopeContent($query, $value)
    {

        $query->where('content', 'LIKE', '%' . $value . '%');
    }

    public function scopeYear(Builder $query, $value)
    {

        $query->whereYear('created_at', $value);
    }

    public function scopeMonth(Builder $query, $value)
    {

        $query->whereMonth('created_at', $value);
    }

    public function scopeSearch(Builder $query, $values)
    {

        foreach (Str::of($values)->explode(' ') as $key => $value) {
            $query->orWhere('title', 'LIKE', "%{$value}%")
                ->orWhere('content', 'LIKE', "%{$value}%");
        }
    }

    public function scopeCategories(Builder $query, $values)
    {

        //where has restrigira los articulos a los que tienen una categoria asociada



        $query->whereHas('category', function ($q) use ($values) {

            $q->whereIn('slug', explode(',', $values));
        });
    }


    public function scopeAuthors(Builder $query, $values)
    {

        //where has restrigira los articulos a los que tienen una categoria asociada

        $query->whereHas('user', function ($q) use ($values) {

            $q->whereIn('name', explode(',', $values));
        });
    }
}
