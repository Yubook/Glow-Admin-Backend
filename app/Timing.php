<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timing extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "timings";
}
