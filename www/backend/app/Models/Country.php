<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $appends = [
        //
    ];

    protected $fillable = [
        'code', 'name', 'phonecode', 'status'
    ];

    protected $casts = [
        //
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public $timestamps = true;
}
