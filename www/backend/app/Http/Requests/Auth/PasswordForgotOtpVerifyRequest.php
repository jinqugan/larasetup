<?php
/**
 * Request : PasswordForgotOtpVerifyRequest.
 *
 * This file used to handle ForgotPassword verify otp validations
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

class PasswordForgotOtpVerifyRequest extends FormRequest
{
    use UserTrait;

    private $customValidator;
    private $countryCodeRules;
    private $usernameRules;
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
            'otp_id' => 'required|exists:one_time_passwords,id',
            'otp' => 'required|digits:6|otp|otp_expiry',
        ];
    }
    public function messages()
    {
        return[
            'otp_id.required' => trans('general.field_required'),
            'otp_id.exists' => trans('passwords.otpid_not_exist'),
            'otp.required' => trans('general.field_required'),
            'otp.digits' => trans('passwords.otp_digit_not_match'),
            'otp.otp' => trans('passwords.otp_notfound'),
            'otp.otp_expiry' => trans('passwords.otp_expired'),
        ];
    }

    /**
     * Handle after validation rules success
     */
    public function withValidated($validator)
    {
        //
    }


    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        $this->userOtp = $this->user->getOtpById($this->get('otp_id'));

        /**
         * Handle phone otp validity
         */
        $this->customValidator->extend('otp', function ($attribute, $value, $parameters, $validator) {
            $otp = $value;

            if (!empty($this->userOtp->otp) && $this->userOtp->otp !== $otp) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');

        /**
         * Handle user when otp is expired (Have to request new otp).
         */
        $this->customValidator->extend('otp_expiry', function ($attribute, $value, $parameters) {
            if (!empty($this->userOtp->expired_at) && strtotime($this->userOtp->expired_at) <= time()) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');
    }
}
