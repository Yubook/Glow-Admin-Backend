<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IsChatEnable extends Model
{
    protected $guarded = [];
    protected $table = 'is_chat_enables';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function barber()
    {
        return $this->hasOne('App\User', 'id', 'barber_id');
    }
}
