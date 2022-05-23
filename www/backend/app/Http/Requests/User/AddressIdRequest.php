<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Factory as CustomValidator;
use App\Traits\UserTrait;
use App\Repositories\User\UserInterface;
use Illuminate\Validation\Rule;

class AddressIdRequest extends FormRequest
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
        $address = $this->userModel->getAddressById($this->route('address'));

        if (empty($address->user_id) || ($address->user_id !== $this->user()->id)) {
            $this->exceptionError = trans('address.view_address_unauthorize');
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
            //
        ];
    }

    public function messages()
    {
        return[
            //
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
