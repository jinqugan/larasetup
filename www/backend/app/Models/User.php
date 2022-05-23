<?php
/**
 * Model : OneTimePassword.
 *
 * This file used to handle Users table
 *
 * @author JQ Gan <jinqgan@gmail.com>
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use DateTimeInterface;
use App\Traits\UuidTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens, HasFactory, SoftDeletes, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'email', 'email_verified_at','password', 'mobileno', 'status_id',
        'last_login_at', 'remember_token', 'country_id', 'mobile_verified_at', 'last_login_ip'
    ];

    protected $casts = [
        //
    ];

    protected $appends = [
        //
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * Override findForPassport function and custom username field check
     *
     * @param  string  $username
     * @return Boolean
     */
    public function findForPassport($username)
    {
        return $this->where('id', $username)->first();
    }

    /**
     * Override possport function and custom password check
     *
     * @param  string  $password
     * @return Boolean
     */
    public function validateForPassportPasswordGrant($password)
    {
        if ($password === 'bypass') {
            return true;
        }

        return Hash::check($password, $this->password);
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status','status_id','id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function primaryAddress()
    {
        return $this->hasOne(Address::class)->where(['default' => 1]);
    }
}
