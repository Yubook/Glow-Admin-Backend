<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceUser extends Pivot
{
    protected $guarded = [];
    protected $table = "service_user";

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function driver()
    {
        return $this->belongsTo('App\User');
    }

    public function service()
    {
        return $this->belongsTo('App\Service');
    }
   
}
