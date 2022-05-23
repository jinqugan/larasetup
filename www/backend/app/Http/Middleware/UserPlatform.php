<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ResponseTrait;

class UserPlatform
{
    use ResponseTrait;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function handle($request, Closure $next)
    {
        $source = strtolower($request->header(HEADER_SOURCE));

        if (!in_array($source, config('constant.source_authentication'))) {
            $result = $this->requestErrors();
            $result['message'] = trans('general.header_source_invalid');

            return response()->json($result, $this->notFoundStatus);
        }

        return $next($request);
    }
}

