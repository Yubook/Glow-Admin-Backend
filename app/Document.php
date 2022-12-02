<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = 'documents';

    public function galleryable()
    {
        return $this->morphTo();
    }
}
