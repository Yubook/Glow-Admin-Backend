<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCancleReason extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "order_cancle_reasons";

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo('App\User', 'driver_id');
    }

    public function cancleBy()
    {
        return $this->belongsTo('App\User', 'cancle_by');
    }

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }
}
