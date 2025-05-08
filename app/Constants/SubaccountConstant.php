<?php

namespace App\Constants;

class SubaccountConstant
{
    public const STATUSES = [
        'All',
        'Active',
        'Inactive',
    ];

    public static function getStatuses()
    {
        return self::STATUSES;
    }
}
