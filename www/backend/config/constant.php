<?php
/**
 * Config : constant variables
 * This file is used to handle custom contant values.
 *
 * Do not use helper function in config file. eg. url(), asset()
 *
 * @author JQ Gan <jinqgan@gmail.com>
 */

define('HEADER_USERTYPE', 'User-Type');
define('HEADER_USERAGENT', 'User-Agent');
define('HEADER_SOURCE', 'Source');
define('DATABASE_PATH', base_path('database'));
define('DATE_TIME', 'Y-m-d H:i:s');
define('DATE', 'Y-m-d');
define('PAGINATION', 20);
define('BASE_URL', env('APP_URL'));
define('FRONTEND_URL', env('FRONTEND_URL'));
define('SOURCE_WEB', 'web');
define('SOURCE_ANDROID', 'android');
define('SOURCE_IOS', 'ios');
define('ENTRY_PROFILE', 'profile');
define('ENTRY_REGISTER', 'register');
define('ENTRY_LOGIN', 'login');
define('ENTRY_PASSWORD_FORGOT', 'forgot_password');
define('SMA_FACEBOOK', 'facebook');
define('SMA_GOOGLE', 'google');
define('SMA_APPLE', 'apple');
define('COUNTRY_CODE', 'MY');
define('PROFILE', 'profile');
define('JSON_WRAP', 'attributes');
define('SPECIALCHAR', '"%*;<>?^`{|}~\\\'#=&');

return [
    'country_code' => COUNTRY_CODE,
    'country_code_digits' => 2,
    'pagination' => 20,
    'phone_code' => '60',
    'source_authentication' => [SOURCE_WEB, SOURCE_ANDROID, SOURCE_IOS],
    'source_authentication_scope' => [SOURCE_WEB => 'web app user', SOURCE_ANDROID => 'mobile app user', SOURCE_IOS => 'mobile app user'],
    'otp_request_in' => '+30 seconds',
    'otp_expiry_in' => '+15 minutes',
    'otp_record_valid_after_expired' => '+15 minutes',
    'otp_max_retry' => false,
    'otp_retry_period' => [
        0 => ['time' => 30, 'unit' => 'seconds'],
        1 => ['time' => 1, 'unit' => 'minutes'],
        2 => ['time' => 2, 'unit' => 'minutes'],
        3 => ['time' => 4, 'unit' => 'minutes'],
    ],
    'devices_require_token' => [SOURCE_ANDROID, SOURCE_IOS],
    'source_token_expiry_in' => [SOURCE_WEB => '+30 minutes', SOURCE_ANDROID => '+1 hours', SOURCE_IOS => '+1 hours'],
    'source_refresh_token_expiry_in' => [SOURCE_WEB => '+7 days', SOURCE_ANDROID => '+3 months', SOURCE_IOS => '+3 months'],
];
