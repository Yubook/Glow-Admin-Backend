<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "orders";

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function barber()
    {
        return $this->belongsTo('App\User');
    }

    public function service_timings()
    {
        return $this->hasMany('App\OrderServiceSlot', 'order_id', 'id');
    }

    public function review()
    {
        return $this->hasMany('App\Review', 'order_id', 'id');
    }

    public function deleted_service_timings()
    {
        return $this->hasMany('App\OrderServiceSlot', 'order_id', 'id')->withTrashed();
    }
}
