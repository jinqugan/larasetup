<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ResponseTrait;
use App\Http\Resources\User\StatusResource;
use App\Traits\ResourceTrait;

class AddressResource extends JsonResource
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

        $address = [
            'address_id' => $this->id,
            'user_id' => $this->user_id,
            // 'country_id' => $this->country_id,
            'title' => $this->title,
            'phone' => $this->mobileno,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'state' => new StateResource($this->state),
            'city' => new CityResource($this->city),
            'postcode' => $this->postal_code,
            'default' => $this->default,
            'created_at' => $this->created_at->toDateTimeString() ?? null
        ];

        return $address;
    }
}
