<?php

namespace App\Traits;

use Exception;

trait ResponseTrait {

    /**
     * Http response status code
     */
    protected $successStatus = 200;
    protected $badRequestStatus = 400;
    protected $unauthorizedStatus = 401;
    protected $notFoundStatus = 404;
    protected $unprocessableStatus = 422;

    /**
     * Generate successful response array.
     *
     * @return bool
     */
    public function requestResponses()
    {
        return [
            'success' => true,
            'message' => NULL,
            'response' => NULL,
        ];
    }

    /**
     * Generate fail response array.
     *
     * @return bool
     */
    public function requestErrors()
    {
        return [
            'success' => false,
            'message' => NULL,
            'error_code' => NULL,
            'errors' => [],
            'response' => NULL,
        ];
    }
}