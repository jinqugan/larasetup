<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Cache;

trait CacheTrait {
    protected $modelKeyBy = null;
    protected $cacheByUser = null;

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return collection
     */
    public function getCacheModel($model, $statement=null, $forget=0)
    {
        $class = get_class($model);
        $query = null;
        $userId = !empty($this->cacheByUser) ? auth()->user()->id : null;

        if ($statement) {
            ksort($statement);
            $query = http_build_query($statement);
        }

        $cacheKey = $userId.$class.$query;

        if ($forget) {
            Cache::forget($cacheKey);
        }

        return Cache::rememberForever($cacheKey, function () use ($model, $statement) {
            $modelQuery = $model::query();

            if ($statement) {
                $modelQuery->where($statement);
            }

            $data = $modelQuery
            ->get();

            if ($this->modelKeyBy) {
                $data->keyBy($this->modelKeyBy);
            }

            return $data;
        });
    }
}