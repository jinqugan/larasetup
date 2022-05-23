<?php
/**
 * Request : PasswordForgotOtpRequest.
 *
 * This file used to handle ForgotPassword send otp validations
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

class PasswordForgotOtpRequest extends FormRequest
{
    use UserTrait;

    protected $responseErrors;
    private $customValidator;
    private $usernameRules = [];
    private $countryCodeRules = [];
    private $user;
    private $userOtp;
    private $accountStatuses;
    private $userAccount;

    public function __construct(CustomValidator $customValidator, UserInterface $user)
    {
        $this->customValidator = $customValidator;
        $this->user = $user;
        $this->usernameRules = '';
        $this->countryCodeRules = '';
        $this->responseErrors = [];
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {

        return [
            'username.required' => trans('general.field_required'),
            'username.numeric' => trans('passwords.only_allow_numeric_input'),
            'username.phone' => trans('passwords.invalid_phone_format'),
            'username.email' => trans('passwords.invalid_email_format'),
            'username.exists' => trans('passwords.username_not_exist'),
            'username.request_otp' => trans('general.too_many_attempts'),
            'username.login_status' => trans('passwords.invalid_account_status'),
            'country_code.size' => trans('passwords.size_limit'),
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

            if (!empty($this->userAccount)) {
                $this->request->set('user_id', $this->userAccount->id);
            }

            $this->replace($this->request->all());
        });
    }

    /**
     * Handle username rule for (email. phone) login
     */
    private function username()
    {

        /**
         * Initiate variable
         */
        $this->request->add([
            'entry' => ENTRY_PASSWORD_FORGOT,
            'username_type' => $this->usernameBelongTo($this->get('username'))
        ]);

        if ($this->get('username_type') === 'mobileno') {
            $this->request->set('country_code', $this->get('country_code') ? strtoupper($this->get('country_code')) : config('constant.country_code'));
            $phoneFormat = $this->phoneFormatInternational($this->get('username'), $this->get('country_code'));

            if ($phoneFormat) {
                $this->request->set('username', $phoneFormat);
            }

            $this->usernameRules = 'required|numeric|digits_between:0,100|phone:'.$this->get('country_code').'|exists:users,mobileno|login_status|request_otp';
            $this->countryCodeRules = 'sometimes|required|alphabert|size:'.config('constant.country_code_digits');

        } elseif ($this->get('username_type') === 'email') {

            $this->usernameRules = 'required|max:100|email:rfc,dns|exists:users,email|login_status|request_otp';
        }
    }

    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        $this->userOtp = $this->user->getOtpByUsername($this->get('username'), $this->get('entry'));

        $this->username();

        /**
         * Handle user spam request otp
         */
        $this->customValidator->extend('request_otp', function ($attribute, $value, $parameters) {
            if (empty($this->userOtp)) {
                $this->userOtp = $this->user->getOtpByUsername($this->get('username'), $this->get('entry'));
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

        $this->customValidator->extend('login_status', function ($attribute, $value, $parameters) {
            $this->accountStatuses = $this->user->accountStatus()->keyBy('id');
            $this->userAccount = $this->user->getUserByCredential($this->get('username_type'), $this->get('username'));

            if (empty($this->userAccount)) {
                return false;
            }

            if (empty($this->accountStatuses[$this->userAccount['status_id']]['access'])) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');
    }
}
