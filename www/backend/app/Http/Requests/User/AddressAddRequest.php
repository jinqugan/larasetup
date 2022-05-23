<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Factory as CustomValidator;
use App\Traits\UserTrait;
use App\Models\City;
use App\Models\State;
use App\Repositories\Country\CountryInterface;

class AddressAddRequest extends FormRequest
{
    use UserTrait;

    private $customValidator;
    private $city;
    private $state;
    private $countryModel;

    public function __construct(CustomValidator $customValidator, City $city, State $state, CountryInterface $countryModel)
    {
        $this->customValidator = $customValidator;
        $this->responseErrors = [];
        $this->city = $city;
        $this->state = $state;
        $this->countryModel = $countryModel;
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
            'title' => 'sometimes|nullable|string|max:255',
            'country_id' => 'sometimes|nullable|numeric|exists:countries,id',
            'country_code' => 'sometimes|alphabert|size:'.config('constant.country_code_digits'),
            'phone' => ['required', 'numeric', 'digits_between:9,15', 'phone:'.$this->get('country_code')],
            'address_1' => 'required|min:5|max:255|specialcharacter',
            'address_2' => 'sometimes|nullable|min:6|max:255|specialcharacter',
            'state_id' => ['required', 'numeric', 'country_state'],
            'city_id' => ['required', 'numeric', 'state_city'],
            'postcode' => ['required', 'digits:5']
        ];
    }

    public function messages()
    {
        return [
            'title.max'=> trans('address.title_max_value'),
            'country_code.size' => trans('address.size_limit'),
            'phone.required' => trans('general.field_required'),
            'phone.digits_between' => trans('address.mobileno_digit_outofrange'),
            'phone.phone' => trans('address.invalid_phone_format'),
            'address_1.required' => trans('general.field_required'),
            'address_1.max' => trans('address.address_max_value'),
            'country_id.required' => trans('general.field_required'),
            'country_id.numeric' => trans('address.only_numeric_allowed'),
            'country_id.exists' => trans('address.country_id_not_found'),
            'state_id.required' => trans('general.field_required'),
            'state_id.numeric' => trans('address.only_numeric_allowed'),
            'state_id.country_state' => trans('address.state_id_invalid'),
            'city_id.required' => trans('general.field_required'),
            'city_id.numeric' => trans('address.only_numeric_allowed'),
            'city_id.state_city' => trans('address.city_id_invalid'),
            'postcode.required' => trans('general.field_required'),
            'postcode.digits' => trans('address.postcode_digit_notmatch'),
        ];
    }

    private function getExistRule($tableName, $column)
    {
        return 'exists:'.$tableName.','.$column;
    }

    private function address()
    {
        $this->request->set('country_id', $this->get('country_id') ?? config('constant.country_id'));
        $this->request->set('country_code', !empty($this->get('country_code')) ? strtoupper($this->get('country_code')) : config('constant.country_code'));
        $phoneFormat = $this->phoneFormatInternational($this->get('phone'), $this->get('country_code'));

        if ($phoneFormat) {
            /**
             * Set valid formatted phone number
             */
            $this->request->set('mobile_no', $phoneFormat);
        }
    }

    /**
     * Add custom validation rules
     */
    public function validationFactory()
    {
        $this->address();

        $this->customValidator->extend('country_state', function ($attribute, $value, $parameters) {
            $state = $this->countryModel->getStateById($this->get('state_id'));

            if (empty($state->id)) {
                return false;
            }

            if ($state->country_id != $this->get('country_id')) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');

        $this->customValidator->extend('state_city', function ($attribute, $value, $parameters) {
            $city = $this->countryModel->getCityById($this->get('city_id'));

            if (empty($city->id)) {
                return false;
            }

            if ($city->state_id != $this->get('state_id')) {
                return false;
            }

            return true;
        }, /** error msg handle here */ '');
    }
}
