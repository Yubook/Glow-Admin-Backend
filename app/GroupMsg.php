<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class GroupMsg extends Model
{
    protected $guarded = [];
    protected $table = 'group_msg';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
