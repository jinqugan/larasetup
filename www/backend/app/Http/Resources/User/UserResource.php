<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ResponseTrait;
use App\Http\Resources\User\StatusResource;
use App\Traits\ResourceTrait;

class UserResource extends JsonResource
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
            'user_id' => $this->id,
            'fullname' => $this->fullname ?? null,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'country_id' => $this->country_id,
            'phone' => $this->mobileno,
            'phone_verified_at' => $this->mobile_verified_at,
            'status' => new StatusResource($this->status),
            'created_at' => !empty($this->created_at) ? $this->created_at->toDateTimeString() : null
        ];
    }
}
