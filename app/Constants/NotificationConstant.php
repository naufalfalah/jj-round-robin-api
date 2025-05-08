<?php

namespace App\Constants;

class NotificationConstant
{
    public const TYPES = [
        'new_lead' => [
            'label' => 'New Lead',
            'value' => 'new_lead',
        ],
        'activity_on_lead' => [
            'label' => 'Activity on Lead',
            'value' => 'activity_on_lead',
        ],
    ];

    public static function getTypes(): array
    {
        return array_keys(self::TYPES);
    }
}
