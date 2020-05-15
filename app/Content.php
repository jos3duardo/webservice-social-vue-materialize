<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'title', 'text', 'image','link','data'
    ];

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function likes(){
        return $this->belongsToMany('App\User','likes','content_id','user_id');
    }

    public function getDataAttribute($value){
        return date('H'.'\h'.'i d/m/Y', strtotime($value));
    }

}
