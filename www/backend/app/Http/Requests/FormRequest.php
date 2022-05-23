<?php
/**
 * Request : FormRequest.
 *
 * This file used for FormRequest to handle api request validation with json error
 *
 * @author JQ Gan <jinqgan@gmail.com>
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as CustomFormRequest;
use App\Traits\ResponseTrait;
use Carbon\Carbon;

abstract class FormRequest extends CustomFormRequest
{
    use ResponseTrait;

    protected $responseErrors;
    protected $exceptionError;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $result = $this->requestErrors();

        $errors = [];
        $responses = null;
        $source = $this->header('source') ?? null;
        $validators = $validator->errors()->toArray();

        $failed = $validator->failed();

        foreach ($validators as $key => $value) {
            if ($failed[$key]) {
                $failedRule = array_key_first($failed[$key]);

                if (!empty($this->responseErrors[$failedRule])) {
                    $responses = $this->responseErrors[$failedRule];
                }
            }

            $errors[$key] = $value[0];
        }

        $result['message'] = !empty($errors) ? reset($errors) : NULL;
        $result['errors'] = $errors;

        unset($errors);
        unset($this->responseErrors);

        if ($responses) {
            $result['response'][JSON_WRAP] = $responses;
        }

        throw new HttpResponseException(response()->json($result, $this->unprocessableStatus));
    }

    /**
     * Handle after validation rules success
     */
    public function withValidator($validator)
    {
        if (method_exists($this, 'withValidated')) {
            $this->withValidated($validator);
        }

        $this->request->add(['source' => $this->header(HEADER_SOURCE)]);
        $this->request->set('itemPerPage', $this->get('itemPerPage') ? $this->get('itemPerPage') : PAGINATION);

        $this->replace($this->request->all());
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        //
        
        if (method_exists($this, 'validationFactory')) {
            $this->validationFactory();
        }
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        $result = $this->requestErrors();
        // $result['error_code'] = 'FR001';
        $result['message'] = trans('general.unauthorized');
        $result['exception'] = $this->exceptionError;

        throw new HttpResponseException(response()->json($result, $this->unauthorizedStatus));
    }
}
