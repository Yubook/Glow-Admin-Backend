<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarberService extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "barber_services";

    public function service()
    {
        return $this->belongsTo('App\Service', 'service_id', 'id')->select('id', 'time', 'name', 'category_id', 'subcategory_id', 'image', 'is_active');
    }

    public function barber()
    {
        return $this->belongsTo('App\User', 'barber_id', 'id');
    }
}
