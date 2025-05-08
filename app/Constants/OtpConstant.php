<?php

namespace App\Constants;

class OtpConstant
{
    public const TYPES = [
        'REGISTRATION' => [
            'code' => 'REGISTRATION',
            'name' => 'Registration',
        ],
        'RESET_PASSWORD' => [
            'code' => 'RESET_PASSWORD',
            'name' => 'Reset Password',
        ],
        'CHANGE_PASSWORD' => [
            'code' => 'CHANGE_PASSWORD',
            'name' => 'Change Password',
        ],
    ];
}
