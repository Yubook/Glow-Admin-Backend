<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BarberSlot extends Model
{
    protected $guarded = [];
    protected $table = "barber_slots";

    public function time()
    {
        return $this->belongsTo('App\Timing','timing_id','id')->select('id','time');
    }
}
