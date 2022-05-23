<?php
/**
 * Request : PasswordForgotResetRequest.
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

class PasswordForgotResetRequest extends FormRequest
{
    private $customValidator;

    public function __construct(CustomValidator $customValidator)
    {
        $this->customValidator = $customValidator;
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
            'session_id' => 'required|exists:one_time_passwords,session_id',
            'password' => 'required|min:6|max:100|specialcharacter',
            'password_confirmation' => 'required|same:password',
        ];
    }
    public function messages()
    {
        return[
            'session_id.required' => trans('general.field_required'),
            'session_id.exists' => trans('passwords.sessionid_notfound'),
            'password.required' => trans('passwords.field_required'),
            'password.min' => trans('passwords.password_contain_digit'),
            'password_confirmation.required' => trans('passwords.field_required'),
            'password_confirmation.same' => trans('passwords.password_confirm_not_match'),
        ];
    }

    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        //
    }
}
