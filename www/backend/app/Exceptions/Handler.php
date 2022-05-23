<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (UnauthorizedHttpException $e, $request) {
            $result['message'] = trans('general.unauthorized');
            $result['exception'] = trans('general.access_token_invalid_expired');

            return response()->json($result, $e->getStatusCode());
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            $result['message'] = trans('general.invalid_route');
            $result['exception'] = 'Route not found';
            return response()->json($result, $e->getStatusCode());
        });
    }
}
