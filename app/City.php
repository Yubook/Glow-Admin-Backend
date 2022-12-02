<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = [];
    protected $table = 'cities';

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State', 'state_id');
    }
}
