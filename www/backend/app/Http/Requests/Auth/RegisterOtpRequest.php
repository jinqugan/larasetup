<?php
/**
 * Request : RegisterOtpRequest.
 *
 * This file used to handle ForgotPassword reset new credential
 *
 * @author JQ Gan <jinqgan@gmail.com>
 * 
 * @version 1.0
 */

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Factory as CustomValidator;
use App\Traits\UserTrait;
use App\Repositories\User\UserInterface;
use App\Http\Resources\User\OneTimePasswordResource;

class RegisterOtpRequest extends FormRequest
{
    use UserTrait;

    protected $responseErrors;
    private $customValidator;
    private $countryCodeRules = [];
    private $usernameRules = [];
    private $userOtp;

    public function __construct(CustomValidator $customValidator, UserInterface $user)
    {
        $this->customValidator = $customValidator;
        $this->responseErrors = [];
        $this->user = $user;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country_code' => $this->countryCodeRules,
            'username' => $this->usernameRules,
        ];
    }
    
    public function messages()
    {
        return[
            'country_code.size' => trans('register.size_limit'),
            'username.required' => trans('general.field_required'),
            'username.digits_between' => trans('register.username_digit_limit'),
            'username.phone' => trans('register.invalid_phone_format'),
            'username.email' => trans('register.invalid_email_format'),
            'username.unique' => $this->get('username_type') === 'mobileno' ? trans('register.phone_taken') : trans('register.username_unique'),
            'username.request_otp' => trans('general.too_many_attempts'),
            'username.max' => trans('general.max_input'),
        ];
    }

    /**
     * Handle after validation rules success
     */
    public function withValidated($validator)
    {
        $validator->after(function () {
            if (!empty($this->userOtp)) {
                $this->request->set('old_otp_id', $this->userOtp->id);
            }

            $this->replace($this->request->all());
        });
    }

    /**
     * Handle username rule for (email | phone) register
     */
    private function username()
    {
        $this->request->add([
            'entry' => ENTRY_REGISTER,
            'username_type' => $this->usernameBelongTo($this->get('username'))
        ]);

        if ($this->get('username_type') === 'mobileno') {
            $this->request->set('country_code', !empty($this->get('country_code')) ? strtoupper($this->get('country_code')) : config('constant.country_code'));
            $phoneFormat = $this->phoneFormatInternational($this->get('username'), $this->get('country_code'));

            if ($phoneFormat) {
                $this->request->set('username', $phoneFormat);
            }

            $this->usernameRules = 'required|numeric|digits_between:6,100|phone:'.$this->get('country_code').'|unique:users,mobileno|request_otp';
            $this->countryCodeRules = 'sometimes|required|alphabert|size:'.config('constant.country_code_digits');
        } elseif ($this->get('username_type') === 'email') {
            $this->usernameRules = 'required|max:100|email:rfc,dns|unique:users,email|request_otp';
        }
    }

    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        $this->userOtp = $this->user->getUserEntryOtp($this->get('username'), $this->get('entry'));

        $this->username();

        $this->customValidator->extend('request_otp', function ($attribute, $value, $parameters) {
            if (empty($this->userOtp)) {
                $this->userOtp = $this->user->getUserEntryOtp($this->get('username'), $this->get('entry'));
            }

            if (!empty($this->userOtp->created_at)) {
                $requestAt = date(DATE_TIME, strtotime($this->userOtp->created_at.' '.config('constant.otp_request_in')));

                if (strtotime($requestAt) > time()) {
                    $this->responseErrors['RequestOtp'] = new OneTimePasswordResource($this->userOtp);

                    return false;
                }
            }

            return true;
        }, /** error msg handle here */ '');
    }
}
