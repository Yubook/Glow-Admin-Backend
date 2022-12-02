<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderServiceSlot extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "order_service_slots";

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function slot()
    {
        return $this->belongsTo('App\BarberSlot');
    }
}
