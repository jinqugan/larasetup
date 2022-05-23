<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Factory as CustomValidator;
use App\Traits\UserTrait;
use App\Repositories\User\UserInterface;

class AddressPrimaryEditRequest extends FormRequest
{
    use UserTrait;

    private $customValidator;
    private $userModel;

    public function __construct(CustomValidator $customValidator, UserInterface $userModel)
    {
        $this->customValidator = $customValidator;
        $this->responseErrors = [];
        $this->exceptionError = null;
        $this->userModel = $userModel;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->address = $this->userModel->getAddressById($this->route('address'));

        if (empty($this->address->user_id) || ($this->address->user_id !== $this->user()->id)) {
            $this->exceptionError = trans('address.edit_primary_address_unauthorize');
            return false;
        }

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
            'address_id' => 'required|primary',
        ];
    }

    public function messages()
    {
        return[
            'address_id.required'=> trans('address.address_id_required'),
            'address_id.primary'=> trans('address.same_default_address'),
        ];
    }

    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        $this->request->add(['address_id' => $this->route('address')]);

        $this->customValidator->extend('primary', function ($attribute, $value, $parameters) {
            if (!empty($this->address->default)) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');
    }
}
