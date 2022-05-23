<?php
/**
 * Interface : UserInterface.
 *
 * This file used to initialise all user related activities
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Repositories\User;

use Illuminate\Http\Request;

interface UserInterface
{
    /**
     * Register new user's account
     */
    public function userRegister(Request $request);

    /**
     * Login existing user's account
     */
    public function userLogin(Request $request);

    /**
     * get user requested otp by entry
     */
    public function getUserEntryOtp($username, $entry);

    /**
     * request username's OTP
     */
    public function requestOtp(Request $request);

    /**
     * verify username's OTP
     */
    public function verifyOtp(Request $request);

    /**
     * resend username's OTP
     */
    public function resendOtp(Request $request);

    /**
     * return otp data by username type
     */
    public function getOtpByUsername($username, $entry);

    /**
     * get otp data by otp id
     */
    public function getOtpById($otpId);

    /**
     * get otp data by session id
     */
    public function getOtpBySessionId($sessionId);
    
    /**
     * delete one time password record by id
     */
    public function deleteOtpById($otpId);

    /**
     * Return account status list
     */
    public function accountStatus();

    /**
     * Refresh access token by refresh token
     */
    public function refreshAccessToken($refreshToken);

    /**
     * get user data by email address
     */
    public function getUserByEmail($email);

    /**
     * verified user account when profile is verified
     */
    public function activateUser();

    /**
     * insert record for new device login.
     */
    public function findOrNewDevice(array $params);

    /**
     * get user data by credential provided
     */
    public function getUserByCredential($usertype, $username);

    /**
     * user reset forgotten password
     */
    public function resetPassword($userId, $password);

    /**
     * user change old password
     */
    public function changePassword($password);

    /**
     * get user addresses
     */
    public function getAddress(Request $request);

     /**
     * get user addresses by id
     */
    public function getAddressById($addressId);

    /**
     * get user primary address
     */
    public function getPrimaryAddress();

    /**
     * get user primary address
     */
    public function editPrimaryAddress($addressId);

    /**
     * add new address
     */
    public function addAddress(Request $request);

    /**
     * Edit existing address by id
     */
    public function editAddress(Request $request, $addressId);

    /**
     * Delete existing address by id
     */
    public function deleteAddress($addressId);
}
