<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Factory as CustomValidator;
use App\Traits\UserTrait;
use App\Models\City;
use App\Models\State;
use App\Repositories\Country\CountryInterface;
use App\Repositories\User\UserInterface;
use Illuminate\Validation\Rule;

class AddressDeleteRequest extends FormRequest
{
    use UserTrait;

    private $customValidator;
    private $city;
    private $state;
    private $countryModel;
    private $cityData;
    private $address;

    public function __construct(CustomValidator $customValidator, City $city, State $state, CountryInterface $countryModel, UserInterface $userModel)
    {
        $this->customValidator = $customValidator;
        $this->responseErrors = [];
        $this->exceptionError = null;
        $this->city = $city;
        $this->state = $state;
        $this->countryModel = $countryModel;
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
            $this->exceptionError = trans('address.delete_address_unauthorize');
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
            'address_id.required'=> trans('general.field_required'),
            'address_id.primary'=> trans('address.unable_delete_default_address'),
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
