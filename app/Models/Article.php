<?php

namespace App\Models;

use App\Models\Traits\HasSorts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{



    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];



  public $allowedSorts=['title','content'];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'category_id' => 'integer',
        'user_id' => 'integer',
    ];


    // protected $fillable=[
    //     'title','slug','content','category_id','user_id'
    // ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }



}
