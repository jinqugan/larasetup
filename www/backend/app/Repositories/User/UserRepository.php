<?php
/**
 * Repository : UserRepository.
 *
 * This file used to handling all user related activities, which all in UserInterface
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Repositories\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\UserTrait;
use App\Traits\CacheTrait;

class UserRepository implements UserInterface
{
    use UserTrait, CacheTrait;

    // Our Eloquent models
    protected $userModel;
    protected $otpModel;
    protected $deviceModel;
    protected $statusModel;
    protected $genderModel;
    protected $addressModel;

    /**
     * ## USER STATUS ##
     * pending for not verified account
     * active for verified account & able to login
     * inactive for verified account & limited access
     * disabled for account that unable to login and required admin to open back
     * suspended for account banned for life time.
     */

    /**
     * Setting our class to the injected model.
     *
     * @param Model
     *
     * @return UserRepository
     */
    public function __construct(
        Model $user,
        Model $otp,
        Model $status,
        Model $device,
        Model $address
    ) {
        $this->userModel = $user;
        $this->otpModel = $otp;
        $this->statusModel = $status;
        $this->deviceModel = $device;
        $this->addressModel = $address;
    }

    /**
     * Register customer.
     *
     * @param Request $request
     *
     * @return Model
     */
    public function userRegister($request)
    {
        $createdAt = Carbon::now();

        $userData['email'] = $request['email'] ?? null;
        $userData['fullname'] = $request['fullname'] ?? null;
        $userData['firstname'] = $request['firstname'] ?? null;
        $userData['lastname'] = $request['lastname'] ?? null;
        $userData['mobileno'] = $request['mobileno'] ?? null;
        $userData['image'] = $request['avatar'] ?? null;
        $userData['password'] = $request['password'];
        $userData['no_password'] = $request['no_password'] ?? 0;
        $userData['usergroup_id'] = $request['usergroup_id'] ?? 4;
        $userData['country_id'] = $request['country_id'] ?? config('constant.country_id');
        $userData['last_login_at'] = $createdAt;
        $userData['last_login_ip'] = $request->ip();

        $statuses = $this->accountStatus()->keyBy('name');

        $userData['status_id'] = $statuses['pending']['id'];

        if ($userData['email']) {
            $userData['email_verified_at'] = $createdAt;
        }

        if ($userData['mobileno']) {
            $userData['mobile_verified_at'] = $createdAt;
        }

        if (!empty($request['old_otp_id'])) {
            $this->deleteOtpById($request['old_otp_id']);
        }

        $user = $this->userModel
        ->create($userData);

        return $user;
    }

    /**
     * Set user's account status
     *
     * @param int $id
     *
     * @return void
     */
    public function activateUser()
    {
        $user = Auth::user();

        $statuses = $this->accountStatus()->keyBy('name');

        if (($user->email_verified_at && $user->mobile_verified_at) && $user->status_id == $statuses['pending']['id']) {
            $this->userModel->where(['id' => $user->id])->update(['status_id' => $statuses['active']['id']]);
        }
    }

    /**
     * Register customer.
     *
     * @param Request $request
     *
     * @return Model
     */
    public function userLogin($request)
    {
        $createdAt = Carbon::now();
        $userIuserDatanfo['remember_token'] = $request['remember_token'] ?? null;
        $userData['last_login_at'] = $createdAt;
        $userData['last_login_ip'] = $request->ip();
        
        $success = $this->userModel
        ->where(['id' => $request['user_id']])
        ->update($userData);

        return $success;
    }

    /**
     * get user requested otp data of entry
     *
     * @param String $username [mobileno, email]
     * @param String $entry ['register', 'forgot password']
     *
     * @return Model
     */
    public function getUserEntryOtp($username, $entry)
    {
        return $this->otpModel
        ->where(['username' => $username, 'entry' => $entry])->first();
    }

    /**
     * generate OTP for user
     *
     * @param Array $request
     *
     * @return Model
     */
    public function requestOtp($request)
    {
        $createdAt = date(DATE_TIME);
        $retry = 0;
        $retryPeriod = config('constant.otp_retry_period');
        $time = isset($retryPeriod[$retry]) ? $retryPeriod[$retry] : $retryPeriod[count($retryPeriod) - 1];
        $duration = '+' . $time['time'] . ' ' . $time['unit'];

        if (!empty($request['form_data'])) {
            $formData = json_decode($request['form_data'], true);
        }

        $formData['resend_in'] = $time;

        if ($formData) {
            $request['form_data'] = json_encode($formData);
        }

        if (!empty($request['old_otp_id'])) {
            $this->deleteOtpById($request['old_otp_id']);
        }

        $userOtp = $this->otpModel->create([
            'user_id' => $request['user_id'] ?? null,
            'username' => $request['username'],
            'entry' => $request['entry'] ?? null,
            'user_type' => $request['username_type'],
            'otp' => $request['otp'],
            'form_data' => !empty($request['form_data']) ? $request['form_data'] : null,
            'resend_at' => date(DATE_TIME, strtotime($createdAt . ' ' . $duration)),
            'expired_at' => date(DATE_TIME, strtotime($createdAt . ' ' . config('constant.otp_expiry_in'))),
        ]);

        return $userOtp;
    }

    /**
     * resend OTP for user
     *
     * @param Request $request
     *
     * @return Model
     */
    public function resendOtp($request)
    {
        $currentOtp = $this->otpModel->where(['id' => $request['otp_id']])->first();

        $createdAt = date(DATE_TIME);
        $formData = [];

        $retry = isset($currentOtp['retry']) ? $currentOtp['retry'] + 1 : 0;
        $retryPeriod = config('constant.otp_retry_period');

        $time = isset($retryPeriod[$retry]) ? $retryPeriod[$retry] : $retryPeriod[count($retryPeriod) - 1];
        $duration = '+' . $time['time'] . ' ' . $time['unit'];

        if (!empty($currentOtp->form_data)) {
            $currentFormData = json_decode($currentOtp->form_data, true);
            $formData = array_merge($formData, $currentFormData);
        }

        $formData['resend_in'] = $time;

        if ($formData) {
            $request['form_data'] = json_encode($formData);
        }

        $userOtp = tap($this->otpModel::find($request['otp_id']))->update([
            'otp' => $request['otp'],
            'retry' => $retry,
            'form_data' => !empty($request['form_data']) ? $request['form_data'] : null,
            'resend_at' => date(DATE_TIME, strtotime($createdAt . ' ' . $duration)),
            'expired_at' => date(DATE_TIME, strtotime($createdAt . ' ' . config('constant.otp_expiry_in'))),
        ]);

        return $userOtp;
    }

    /**
     * verify OTP for user
     *
     * @param Request $request
     *
     * @return Model
     */
    public function verifyOtp($request)
    {
        $createdAt = date(DATE_TIME);

        $userOtp = tap($this->otpModel->find($request['otp_id']))
        ->update([
            'session_id' => $this->generateRandomToken(),
            'verified_at' => $createdAt,
            'expired_at' => $createdAt,
        ]);

        if($userOtp['user_id'] ?? null) {
            switch ($userOtp['user_type']) {
                case 'email':
                    $this->userModel::find($userOtp['user_id'])->update(['email_verified_at' => $createdAt]);
                    break;

                case 'mobileno':
                    $this->userModel::find($userOtp['user_id'])->update(['phone_verified_at' => $createdAt]);
                    break;

                default:
                    //
                    break;
            }
        }

        return $userOtp;
    }

    /**
     * Returns the otp object associated with the passed id.
     *
     * @param String $id
     *
     * @return Model
     */
    public function getOtpById($otpId)
    {
        return $this->otpModel::find($otpId);
    }

    /**
     * Returns the otp object associated with the passed valid session id.
     *
     * @param String $sessionId
     *
     * @return Model
     */
    public function getOtpBySessionId($sessionId)
    {
        return $this->otpModel::where(['session_id' => $sessionId])->first();
    }

    public function deleteOtpById($id)
    {
        return $this->otpModel->where('id', $id)->delete();
    }

    public function accountStatus()
    {
        $value = Cache::rememberForever('account_statuses', function () {
            return $this->statusModel
            ->where(['type' => 'account'])
            ->get();
        });

        return $value;
    }

    /**
     * refresh access token by refresh token when access token is expiring soon or expired
     *
     * @param String $refreshToken
     *
     * @return Model
     */
    public function refreshAccessToken($refreshToken)
    {
        $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->first();

        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
        ];

        $request = Request::create('/oauth/token', 'POST', $data);
        $oauthToken = json_decode(app()->handle($request)->getContent());

        return $oauthToken;
    }

    /**
     * get user data by email address
     *
     * @param String $email
     *
     * @return Model
     */
    public function getUserByEmail($email)
    {
        if (!$email) {
            return null;
        }

        return $this->userModel
        ->where('email', $email)
        ->first();
    }

    /**
     * get user data by credential (username, password)
     *
     * @param String $usertype
     * @param String $username
     *
     * @return Model
     */
    public function getUserByCredential($usertype, $username)
    {
        return $this->userModel
        ->where($usertype, $username)
        ->first();
    }

    /**
     * find or create new device
     *
     * @param array $params
     *
     * @return Model
     */
    public function findOrNewDevice($params)
    {
        if (empty($params['device_id'])) {
            return null;
        }

        $device = $this->deviceModel->firstOrCreate(
            ['user_id' =>  $params['user_id'], 'device_id' => $params['device_id']],
            ['platform' => $params['platform']]
        );

        return $device;
    }

    /**
     * Returns the otp object associated with the passed username.
     *
     * @param String $username [phone, email]
     *
     * @return Model
     */
    public function getOtpByUsername($username, $entry)
    {
        return $this->otpModel
        ->where(['username' => $username, 'entry' => $entry])
        ->first();
    }

    /**
     * Guest user reset forgotten password
     *
     * @param Request $request
     *
     * @return Model
     */
    public function resetPassword($userId, $password)
    {
        return $this->userModel
        ->find($userId)
        ->update(['password' => bcrypt($password)]);
    }

    /**
     * User reset old password
     *
     * @param Request $request
     *
     * @return Model
     */
    public function changePassword($password)
    {
        return $this->userModel
        ->find(Auth::id())
        ->update(['password' => bcrypt($password)]);
    }

    /**
     * get user addresses
     *
     * @param Numeric $addressId
     *
     * @return Model
     */
    public function getAddressById($addressId)
    {
        return $this->addressModel
        ->find($addressId);
    }

    /**
     * get user addresses
     *
     * @param Request $request
     *
     * @return Model
     */
    public function getAddress(Request $request)
    {
        $itemPerPage = $request['itemPerPage'] ?? PAGINATION;

        if (empty($request['paging'])) {
            $statement = ['user_id' => Auth::id()];

            return $this->getCacheModel($this->addressModel, $statement);
        }

        return $this->addressModel
        ->where('user_id', Auth::id())
        ->paginate($itemPerPage);
    }

    /**
     * get user primary address
     *
     * @param Request $request
     *
     * @return Model
     */
    public function getPrimaryAddress()
    {
        return auth()->user()->primaryAddress;
    }

    /**
     * edit user primary address
     *
     * @param Request $request
     *
     * @return Model
     */
    public function editPrimaryAddress($addressId)
    {
        $address = $this->addressModel
        ->find($addressId);

        $this->addressModel
        ->where(['user_id' => $address->user_id, 'type' => $address->type])
        ->update(['default' => 0]);

        $address
        ->update(['default' => 1]);

        $statement = ['user_id' => Auth::id()];
        $this->getCacheModel($this->addressModel, $statement, 1);

        return $address;
    }

    /**
     * add new address
     *
     * @param Request $request
     *
     * @return Model
     */
    public function addAddress(Request $request)
    {
        $addressData['user_id'] = $request['user_id'] ?? Auth::id();
        $addressData['country_id'] = $request['country_id'] ?? null;
        $addressData['title'] = $request['title'] ?? null;
        $addressData['type'] = $request['type'] ?? 'shipping_address';
        $addressData['mobileno'] = $request['mobile_no'] ?? null;
        $addressData['address_1'] = $request['address_1'] ?? null;
        $addressData['address_2'] = $request['address_2'] ?? null;
        $addressData['city_id'] = $request['city_id'] ?? null;
        $addressData['state_id'] = $request['state_id'] ?? null;
        $addressData['postal_code'] = $request['postcode'] ?? null;
        $addressData['default'] = Auth::user()->addresses->isEmpty() ? 1 : 0;

        $address = $this->addressModel
        ->create($addressData);

        $statement = ['user_id' => Auth::id()];
        $this->getCacheModel($this->addressModel, $statement, 1);

        return $address;
    }

    /**
     * edit existing address
     *
     * @param Request $request
     *
     * @return Model
     */
    public function editAddress(Request $request, $addressId)
    {
        $addressData['country_id'] = $request['country_id'] ?? config('constant.country_id');
        $addressData['title'] = $request['title'] ?? null;
        $addressData['name'] = $request['nickname'] ?? null;
        $addressData['mobileno'] = $request['mobile_no'] ?? null;
        $addressData['address_1'] = $request['address_1'] ?? null;
        $addressData['address_2'] = $request['address_2'] ?? null;
        $addressData['city_id'] = $request['city_id'] ?? null;
        $addressData['state_id'] = $request['state_id'] ?? null;
        $addressData['postal_code'] = $request['postcode'] ?? null;

        $address = tap($this->addressModel
        ->find($addressId))
        ->update($addressData);

        $statement = ['user_id' => Auth::id()];
        $this->getCacheModel($this->addressModel, $statement, 1);

        return $address;
    }

    public function deleteAddress($addressId)
    {
        $address = $this->addressModel
        ->destroy($addressId);

        $statement = ['user_id' => Auth::id()];
        $this->getCacheModel($this->addressModel, $statement, 1);

        return $address;
    }
}
