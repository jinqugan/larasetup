<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Traits\ResponseTrait;
use App\Traits\ResourceTrait;
use Illuminate\Support\Arr;

class CustomCollection extends ResourceCollection
{
    use ResponseTrait, ResourceTrait;

    protected $statusCode;
    protected $message;
    protected $errors;
    protected $withoutResponse;

     /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $collects)
    {
        $this->collects = $collects;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $states['data'] = !($this->collection->isEmpty()) ? $this->collection : null;

        try {
            $states['pagination'] = [
                'base_url' => $this->url(1),
                'current_item' => $this->count(),
                'item_per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'total_item' => $this->total(),
            ];
        } catch (\Throwable $th) {
        }

        return $states;
    }

    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }
}
