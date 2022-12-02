<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWallet extends Model
{
    protected $guarded = [];
    protected $table = "user_wallets";
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }
}
