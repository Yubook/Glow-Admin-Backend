<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $guarded = [];
    protected $table = "user_reviews";

    public function reviewImages()
    {
        return $this->hasMany('App\ReviewImage','user_reviews_id','id');
    }

    public function fromIdUser()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function toIdUser()
    {
        return $this->belongsTo('App\User', 'barber_id', 'id');
    }

    // public function order()
    // {
    //     return $this->belongsTo('App\Order', 'order_id', 'id');
    // }
}
