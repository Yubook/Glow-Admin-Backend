<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFavouriteBarber extends Model
{
    protected $guarded = [];
    protected $table = "user_favourite_barbers";

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function barber()
    {
        return $this->belongsTo('App\User')->select('id','name','email','mobile','profile','gender','average_rating','total_reviews','latitude','longitude','latest_latitude','latest_longitude');
    }
}
