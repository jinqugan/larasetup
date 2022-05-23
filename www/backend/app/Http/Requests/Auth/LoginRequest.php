<?php
/**
 * Controller : LoginRequest.
 *
 * This file used to handle Login Request
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\RequiredIf;

class LoginRequest extends FormRequest
{
    use UserTrait;

    private $customValidator;
    private $countryCodeRules = [];
    private $usernameRules = [];
    private $userOtp;
    private $accountStatuses;

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
            'password' => 'required|min:6|max:100|specialcharacter',
            'device_id' => [
                new RequiredIf(in_array($this->header(HEADER_SOURCE), config('constant.devices_require_token'))),
                'string'
            ],
        ];
    }

    public function messages()
    {
        return[
            'country_code.size' => trans('login.size_limit'),
            'username.required' => trans('general.field_required'),
            'username.digits_between' => trans('login.username_digit_limit'),
            'username.phone' => trans('login.invalid_phone_format'),
            'username.email' => trans('login.invalid_email_format'),
            'username.credential' => trans('login.invalid_credential'),
            'device_id.required' => trans('general.field_required'),
        ];
    }

    /**
     * Handle after validation rules success
     */
    public function withValidated($validator)
    {
        $fails = $validator->fails();

        $validator->after(function ($validator) use ($fails) {

            if (!$fails) {
                if (empty($this->accountStatuses[$this->user()->status_id]['access'])) {
                    $validator->errors()->add('username', trans('login.invalid_account_status', ['status' => trans($this->accountStatuses[$this->user()->status_id]['lang_code'])]));
                }
            }
        });
    }

    /**
     * Handle username rule for (email | phone) register
     */
    private function username()
    {
        $this->request->add([
            'username_type' => $this->usernameBelongTo($this->get('username'))
        ]);

        if ($this->get('username_type') === 'mobileno') {
            $this->request->set('country_code', !empty($this->get('country_code')) ? strtoupper($this->get('country_code')) : config('constant.country_code'));
            $phoneFormat = $this->phoneFormatInternational($this->get('username'), $this->get('country_code'));

            if ($phoneFormat) {
                $this->request->set('username', $phoneFormat);
            }

            $this->usernameRules = 'required|numeric|digits_between:6,100|phone:'.$this->get('country_code').'|credential';
            $this->countryCodeRules = 'sometimes|required|alphabert|size:'.config('constant.country_code_digits');
        } elseif ($this->get('username_type') === 'email') {
            $this->usernameRules = 'required|max:100|email:rfc,dns|credential';
        }
    }

    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        $this->accountStatuses = $this->user->accountStatus()->keyBy('id');

        $this->username();

        $this->customValidator->extend('request_otp', function ($attribute, $value, $parameters) {
            if (empty($this->userOtp)) {
                $this->userOtp = $this->user->getUserEntryOtp($this->get('username'), $this->get('entry'));
            }

            if (!empty($this->userOtp->created_at)) {
                $requestAt = date(DATE_TIME, strtotime($this->userOtp->created_at.' '.config('constant.otp_request_in')));

                if (strtotime($requestAt) > time()) {
                    $this->responseErrors['RequestOtp'] = $this->userOtp->toArray();

                    return false;
                }
            }

            return true;
        }, /** error msg handle here */ '');

        $this->customValidator->extend('credential', function ($attribute, $value, $parameters) {
            $attempt = Auth::attempt([
                $this->get('username_type') => $this->get('username'),
                'password' => $this->get('password'),
            ]);

            if (!$attempt) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');


        $this->customValidator->extend('login_status', function ($attribute, $value, $parameters) {
            $this->accountStatuses = $this->user->accountStatus()->keyBy('id');

            if (empty($this->accountStatuses[$this->user()->status_id]['access'])) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');
    }
}
