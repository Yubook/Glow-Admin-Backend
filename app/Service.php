<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "services";

    public function category()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
    }

    public function subcategory()
    {
        return $this->hasOne('App\Subcategory', 'id', 'subcategory_id');
    }

}
