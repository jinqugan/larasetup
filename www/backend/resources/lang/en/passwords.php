<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'We have emailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",

    /**
     * Custom Language
     */

    'otpid_not_exist' => 'Otp id you entered could not be found',
    'invalid_phone_format' => 'Phone number format is invalid.',
    'invalid_email_format' => 'Email address format is invalid. Please try again, with the format example@domain.com',
    'only_allow_numeric_input' => 'Only allowed numeric input',
    'username_not_exist' => 'Username you entered could not be found',
    'invalid_account_status' => 'Your account had been :status. Please contact customer service for further support.',
    'size_limit' => 'Only allowed size of :size',
    'otp_sent_success' => 'OTP had been sent out successfully',
    'otp_resent_success' => 'OTP had been resent successfully',
    'otp_verify_success' => 'OTP verified successfully',
    'otp_digit_not_match' => 'OTP digit must be :digits digits',
    'otp_notfound' => 'The OTP you entered could not be authenticated. Please try again.',
    'otp_expired' => 'OTP entered is expired. Please generate a new OTP and try again',
    'sessionid_notfound' => 'Session id you entered could not be found',
    'password_contain_digit' => 'The password must contain at least :min character',
    'password_confirm_not_match' => 'The password confirmation does not match',
    'reset_password_success' => 'You can now sign in with your new password.',
];
