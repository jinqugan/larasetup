<?php

namespace App\Http\Controllers\Api\v1\Countries;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\CountryResource;
use App\Http\Resources\User\StateResource;
use App\Http\Resources\CustomCollection;
use App\Http\Resources\User\CityResource;
use Illuminate\Http\Request;
use App\Repositories\Country\CountryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CountryController extends Controller
{
    protected $country;

    /**
     * Create a new controller instance.
     */
    public function __construct(CountryInterface $country)
    {
        $this->country = $country;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyBys = ['country_id' => 'id', 'code' => 'code'];

        if (!empty($request['key_by'] && in_array($request['key_by'], array_keys($keyBys)))) {
            $request['key_by'] = $keyBys[$request['key_by']];
        } else {
            $request['key_by'] = null;
        }

        $countries = $this->country->countryList($request);

        if ($request['key_by']) {
            if ($countries instanceof LengthAwarePaginator) {
                $newCollections = $countries->getCollection()->keyBy($request['key_by']);

                $countries->setCollection($newCollections);
            } else {
                $countries = $countries->keyBy($request['key_by']);
            }
        }

        return (new CustomCollection($countries, CountryResource::class))
        ->message(trans('country.country_list'));
    }

    public function getState(Request $request)
    {
        $keyBys = ['state_id' => 'id'];

        if (!empty($request['key_by'] && in_array($request['key_by'], array_keys($keyBys)))) {
            $request['key_by'] = $keyBys[$request['key_by']];
        } else {
            $request['key_by'] = null;
        }

        /**
         * Set default country to malaysia
         */
        if (empty($request['country_id']) && empty($request['state_id'])) {
            $request['country_id'] = config('constant.country_id');
        }

        $states = $this->country->stateList($request);

        if ($request['key_by']) {
            if ($states instanceof LengthAwarePaginator) {
                $newCollections = $states->getCollection()->keyBy($request['key_by']);
                $states->setCollection($newCollections);
            } else {
                $states = $states->keyBy($request['key_by']);
            }
        }

        return (new CustomCollection($states, StateResource::class))
        ->message(trans('country.state_list'));
    }

    public function getCity(Request $request)
    {
        $keyBys = ['city_id' => 'id'];

        if (!empty($request['key_by'] && in_array($request['key_by'], array_keys($keyBys)))) {
            $request['key_by'] = $keyBys[$request['key_by']];
        } else {
            $request['key_by'] = null;
        }

        $cities = $this->country->cityList($request);

        if ($request['key_by']) {
            if ($cities instanceof LengthAwarePaginator) {
                $newCollections = $cities->getCollection()->keyBy($request['key_by']);
                $cities->setCollection($newCollections);
            } else {
                $cities = $cities->keyBy($request['key_by']);
            }
        }

        return (new CustomCollection($cities, CityResource::class))
        ->message(trans('country.city_list'));
    }
}
