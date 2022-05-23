<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ResponseTrait;
use App\Traits\ResourceTrait;

class OneTimePasswordResource extends JsonResource
{
    use ResponseTrait, ResourceTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        return [
            'otp_id' => $this->id,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'otp' => $this->otp, //to be hide
            'session_id' => $this->session_id,
            'verified_at' => $this->verified_at,
            'resend_at' => $this->resend_at,
            'expired_at' => $this->expired_at,
            'updated_at' => !empty($this->updated_at) ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
