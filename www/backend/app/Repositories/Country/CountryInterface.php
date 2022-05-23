<?php
/**
 * Interface : CountryInterface.
 *
 * This file used to initialise all country related activities
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Repositories\Country;

use Illuminate\Http\Request;

interface CountryInterface
{
    /**
     * return country list of data
     */
    public function countryList(Request $request=null);

    /**
     * return state list of data
     */
    public function stateList(Request $request=null);

    /**
     * return state list of data
     */
    public function cityList(Request $request=null);

    /**
     * get state data by id
     */
    public function getStateById($stateId);

    /**
     * get city data by id
     */
    public function getCityById($cityId);

    /**
     * check whether city record is exists under state
     */
    public function existCityByStateId($stateId);
}
