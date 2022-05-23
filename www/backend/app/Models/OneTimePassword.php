<?php
/**
 * Model : OneTimePassword.
 *
 * This file used to handle OneTimePasswords table
 *
 * @author JQ Gan <jinqgan@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Traits\UuidTrait;

class OneTimePassword extends Model
{
    use UuidTrait;

    protected $appends = [
        //
    ];

    protected $fillable = [
        'user_id', 'username', 'entry', 'user_type', 'otp', 'retry', 'form_data', 'session_id', 'verified_at', 'resend_at', 'expired_at',
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
        'entry', 'user_type', 'form_data', 'created_at'
    ];

    public $timestamps = true;

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(DATE_TIME);
    }
}
