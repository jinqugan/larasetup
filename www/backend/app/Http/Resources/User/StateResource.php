<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\ResponseTrait;
use App\Traits\ResourceTrait;

class StateResource extends JsonResource
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
            'state_id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->name,
        ];
    }
}
