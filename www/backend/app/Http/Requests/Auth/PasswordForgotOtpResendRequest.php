<?php
/**
 * Request : PasswordForgotOtpResendRequest.
 *
 * This file used to handle ForgotPassword resend otp validations
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

class PasswordForgotOtpResendRequest extends FormRequest
{
    use UserTrait;

    protected $responseErrors;
    private $customValidator;
    private $usernameRules;
    private $countryCodeRules;
    private $user;
    private $userOtp;
    private $accountStatuses;

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
            'otp_id' => 'required|exists:one_time_passwords,id|max_resend_otp|resend_otp',
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
            'otp_id.required' => trans('general.field_required'),
            'otp_id.exists' => trans('passwords.otpid_not_exist'),
            'otp_id.max_resend_otp' => trans('general.reach_max_attempts'),
            'otp_id.resend_otp' => trans('general.too_many_attempts'),
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
     * Handle username rule for (email. phone) login
     */
    private function username()
    {
        $this->request->add([
            /**
             * change in future when enable email
             * current set default only allow register by phone number
             */
            'entry' => ENTRY_PASSWORD_FORGOT,
            'username_type' => $this->usernameBelongTo($this->get('username'))
        ]);
    }

    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        $this->userOtp = $this->user->getOtpById($this->get('otp_id'));

        $this->username();

        $this->customValidator->extend('max_resend_otp', function ($attribute, $value, $parameters) {
            if (empty($this->userOtp)) {
                $this->userOtp = $this->user->getOtpById($this->get('otp_id'));
            }

            $maxRetry = count(config('constant.otp_retry_period')) - 1 ;

            if (config('constant.otp_max_retry') && (isset($this->userOtp->retry) && $this->userOtp->retry >= $maxRetry)) {

                return false;
            }

            return true;
        }, /** error msg handle here */ '');

        $this->customValidator->extend('resend_otp', function ($attribute, $value, $parameters) {
            if (empty($this->userOtp)) {
                $this->userOtp = $this->user->getOtpById($this->get('otp_id'));
            }

            // print_r($this->userOtp);exit;

            if (!empty($this->userOtp->resend_at) && strtotime($this->userOtp->resend_at) > time()) {
                $this->responseErrors['ResendOtp'] = new OneTimePasswordResource($this->userOtp);

                return false;
            }

            return true;
        }, /** error msg handle here */ '');
    }
}
