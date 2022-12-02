<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function documents()
    {
        return $this->morphMany(Document::class, 'galleryable')->orderBy('id', 'desc');
    }

    public function portfolios()
    {
        return $this->hasMany(ReviewImage::class, 'barber_id', 'id')->select('id', 'barber_id', 'path');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function getReviews()
    {
        return $this->hasMany(Review::class, 'barber_id', 'id')->select('id', 'user_id', 'barber_id', 'service', 'hygiene', 'value');
    }

    public function barberServices()
    {
        return $this->hasMany('App\BarberService', 'barber_id', 'id')->where('is_active', 1)->select('id', 'service_id', 'barber_id', 'price', 'is_active');
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'driver_id', 'id');
    }

    public function notification()
    {
        return $this->hasOne('App\Notification', 'user_id', 'id')->orderBy('id', 'desc');
    }

    public function policyAndTerm()
    {
        return $this->hasOne('App\BarberTermsPolicy', 'barber_id', 'id');
    }

    public function wallet()
    {
        return $this->hasOne('App\UserWallet', 'user_id', 'id');
    }
}
