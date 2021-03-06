<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Traits\ResponseTrait;

class Authenticate extends Middleware
{
    use ResponseTrait;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if ($request->header(HEADER_SOURCE) === SOURCE_WEB) {
                return route('login');
            } else {
                $result['message'] = trans('general.unauthorized');

                return response()->json($result, $this->unauthorizedStatus);
            }
        }
    }
}
