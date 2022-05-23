<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $appends = [
        //
    ];

    protected $fillable = [
        'name', 'country_id'
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

    /**
     * Created at & Updated at add.
     */
    public $timestamps = true;
}
