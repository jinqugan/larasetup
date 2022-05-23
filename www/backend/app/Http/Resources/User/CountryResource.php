<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ResponseTrait;
use App\Traits\ResourceTrait;

class CountryResource extends JsonResource
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
            'country_id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'phone_code' => $this->phonecode
        ];
    }
}
