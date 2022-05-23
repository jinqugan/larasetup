<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\User\UserInterface;
use App\Http\Requests\User\AddressIdRequest;
use App\Http\Requests\User\AddressAddRequest;
use App\Http\Requests\User\AddressEditRequest;
use App\Http\Resources\User\AddressResource;
use App\Http\Requests\User\AddressDeleteRequest;
use App\Http\Requests\User\AddressPrimaryEditRequest;
use App\Http\Resources\CustomCollection;

class AddressController extends Controller
{
    protected $user;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $address = $this->user->getAddress($request);


        return (new CustomCollection($address, AddressResource::class))
        ->message(trans('address.address_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\AddressAddRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressAddRequest $request)
    {
        $address = $this->user->addAddress($request);

        return (new AddressResource($address))
        ->message(trans('address.add_address_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AddressIdRequest $request, $id)
    {
        $address = $this->user->getAddressById($id);

        return (new AddressResource($address));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\AddressEditRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AddressEditRequest $request, $id)
    {
        $address = $this->user->editAddress($request, $id);

        return (new AddressResource($address))
        ->message(trans('address.edit_address_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AddressDeleteRequest $request, $id)
    {
        $address = $this->user->deleteAddress($id);

        return (new AddressResource(null))
        ->message(trans('address.delete_address_success'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function primaryAddress()
    {
        $address = $this->user->getPrimaryAddress();

        return (new AddressResource($address))
        ->message(trans('address.primary_address'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editPrimaryAddress(AddressPrimaryEditRequest $request, $addressId)
    {
        $this->user->editPrimaryAddress($addressId);

        return (new AddressResource(null))
        ->message(trans('address.edit_primary_address_success'));
    }
}
