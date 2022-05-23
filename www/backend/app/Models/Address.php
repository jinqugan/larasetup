<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'country_id', 'title', 'type', 'address_1', 'address_2',
        'state_id', 'city_id', 'mobileno', 'postal_code', 'default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function state()
    {
        return $this->hasOne('App\Models\State','id','state_id');
    }

    public function city()
    {
        return $this->hasOne('App\Models\City','id','city_id');
    }

    public function primaryAddress()
    {
        return Address::where(['user_id' => auth()->user()->id, 'default' => 1])->first();
    }
}
