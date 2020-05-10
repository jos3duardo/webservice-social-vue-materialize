<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content_id', 'data','text'
    ];

    public function content(){
        return $this->belongsTo('App\Content');
    }
}
