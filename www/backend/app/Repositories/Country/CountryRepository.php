<?php
/**
 * Repository : CountryRepository.
 *
 * This file used to handling all country related activities, which all in CountryInterface
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Repositories\Country;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Traits\CacheTrait;

class CountryRepository implements CountryInterface
{
    use CacheTrait;

    // Our Eloquent models
    protected $countryModel;
    protected $stateModel;
    protected $cityModel;

    /**
     * Setting our class to the injected model.
     *
     * @param Model
     *
     * @return UserRepository
     */
    public function __construct(
        Model $country,
        Model $state,
        Model $city
    ) {
        $this->countryModel = $country;
        $this->stateModel = $state;
        $this->cityModel = $city;
    }

    /**
     * Return all country list data
     *
     * @return Model
     */
    public function countryList($request=null)
    {
        $itemPerPage = $request['itemPerPage'] ?? PAGINATION;
        $request['country_id'] = (!empty($request['country_id']) && is_array($request['country_id'])) ? array_filter($request['country_id']) : ($request['country_id'] ?? null);

        if (empty($request['paging'])) {
            $statement = ['status' => 'active'];
            $countryQuery = $this->getCacheModel($this->countryModel, $statement)->toQuery();
        } else {
            $countryQuery = $this->countryModel::query();
            $countryQuery->where('status', 'active');
        }

        if (!empty($request['country_id'])) {
            if (is_array($request['country_id'])) {
                $countryQuery->whereIn('id', $request['country_id']);
            } else {
                $countryQuery->where('id', $request['country_id']);
            }
        }

        if (empty($request['paging'])) {
            return $countryQuery
            ->get();
        } else {
            return $countryQuery
            ->paginate($itemPerPage);
        }
    }

    /**
     * Return all state list data
     *
     * @return Model
     */
    public function stateList($request=null)
    {
        $itemPerPage = $request['itemPerPage'] ?? PAGINATION;
        $request['country_id'] = (!empty($request['country_id']) && is_array($request['country_id'])) ? array_filter($request['country_id']) : ($request['country_id'] ?? null);
        $request['state_id'] = (!empty($request['state_id']) && is_array($request['state_id'])) ? array_filter($request['state_id']) : ($request['state_id'] ?? null);

        if (empty($request['paging'])) {
            $stateQuery = $this->getCacheModel($this->stateModel)->toQuery();
        } else {
            $stateQuery = $this->stateModel::query();
        }

        if (!empty($request['country_id'])) {
            if (is_array($request['country_id'])) {
                $stateQuery->whereIn('country_id', $request['country_id']);
            } else {
                $stateQuery->where('country_id', $request['country_id']);
            }
        }

        if ($request['state_id'] ?? null) {
            if (is_array($request['state_id'])) {
                $stateQuery->whereIn('id', $request['state_id']);
            } else {
                $stateQuery->where('id', $request['state_id']);
            }
        }

        if (empty($request['paging'])) {
            return $stateQuery
            ->get();
        } else {
            return $stateQuery
            ->paginate($itemPerPage);
        }
    }

    /**
     * Return all city list data
     *
     * @return Model
     */
    public function cityList($request=null)
    {
        $itemPerPage = $request['itemPerPage'] ?? PAGINATION;
        $request['city_id'] = (!empty($request['city_id']) && is_array($request['city_id'])) ? array_filter($request['city_id']) : ($request['city_id'] ?? null);

        if (empty($request['paging'])) {
            $cityQuery = $this->getCacheModel($this->cityModel)->toQuery();
        } else {
            $cityQuery = $this->cityModel::query();
        }

        if ($request['state_id'] ?? null) {
            if (is_array($request['state_id'])) {
                $cityQuery->whereIn('state_id', $request['state_id']);
            } else {
                $cityQuery->where('state_id', $request['state_id']);
            }
        }

        if ($request['city_id'] ?? null) {
            if (is_array($request['city_id'])) {
                $cityQuery->whereIn('id', $request['city_id']);
            } else {
                $cityQuery->where('id', $request['city_id']);
            }
        }

        if (empty($request['paging'])) {
            return $cityQuery
            ->get();
        } else {
            return $cityQuery
            ->paginate($itemPerPage);
        }
    }

    /**
     * get state data by id
     *
     * @param Numeric $stateId
     *
     * @return Model
     */
    public function getStateById($stateId)
    {
        if (!$stateId) {
            return null;
        }

        return $this->stateModel
        ->find($stateId);
    }

    /**
     * get state data by id
     *
     * @param Numeric $cityId
     *
     * @return Model
     */
    public function getCityById($cityId)
    {
        if (!$cityId) {
            return null;
        }

        return $this->cityModel
        ->find($cityId);
    }

    /**
     * check whether city record is exists under state
     *
     * @param Numeric $cityId
     *
     * @return Model
     */
    public function existCityByStateId($stateId)
    {
        if (!$stateId) {
            return null;
        }

        return $this->cityModel
        ->where('state_id', $stateId)
        ->exists();
    }
}
