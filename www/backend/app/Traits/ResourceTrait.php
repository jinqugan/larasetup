<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Arr;

trait ResourceTrait {

    protected $statusCode;
    protected $message;
    protected $errors;

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode ?? $this->successStatus);

        $success = $response->isSuccessful();

        $result = $success ? $this->requestResponses() : $this->requestErrors();

        if (!$success) {
            $result['errors'] = $this->errors;
        }

        $data = $response->getData(true);

        /**
         * convert empty array to null
         */
        if ($this::$wrap && empty($data[$this::$wrap])) {
            $data[$this::$wrap] =  null;
        } elseif (empty($data)) {
            $data = null;
        }

        /**
         * excluded key in response array
         */
        $excludedList = ['meta'];

        $result['message'] = $this->message;
        $result['response'] = $data ? Arr::except($data, $excludedList) : null;
        $result += ($data ? Arr::only($data, $excludedList) : null);

        $response->setData($result);
    }

    /**
     * Set message for the resource response.
     *
     * @param  string  $text
     * @return $this
     */
    public function message($text)
    {
        $this->message =  $text;

        return $this;
    }

    /**
     * Set status code for the resource response.
     *
     * @param  int  $status
     * @return $this
     */
    public function statusCode($status)
    {
        $this->statusCode =  $status;

        return $this;
    }

    /**
     * Add errors field to the resource response.
     *
     * @param  array  $data
     * @return $this
     */
    public function errors($data)
    {
        $this->errors = $data;

        return $this;
    }
}