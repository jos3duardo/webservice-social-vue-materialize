<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content_id', 'data','text'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function content(){
        return $this->belongsTo('App\Content');
    }

    public function getDataAttribute($value){
        $data = date('H:i d/m/Y', strtotime($value));
        return str_replace(':','h', $data);
    }
}
