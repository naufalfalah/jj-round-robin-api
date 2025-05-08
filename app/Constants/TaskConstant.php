<?php

namespace App\Constants;

class TaskConstant
{
    public const TASK_PRIORITIES = [
        'danger' => [
            'value' => 1,
            'text' => 'Urgent',
            'bgClass' => 'danger',
        ],
        'high' => [
            'value' => 2,
            'text' => 'High',
            'bgClass' => 'warning',
            'textClass' => 'text-dark',
        ],
        'normal' => [
            'value' => 3,
            'text' => 'Normal',
            'bgClass' => 'secondary',
        ],
    ];

    public static function getTaskPriorities()
    {
        return self::TASK_PRIORITIES;
    }

    public const TASK_GROUPS = [
        'to_do' => [
            'code' => 'to_do',
            'name' => 'to do',
        ],
        'pending_for_approval' => [
            'code' => 'pending_for_approval',
            'name' => 'pending for approval',
        ],
        'revision' => [
            'code' => 'revision',
            'name' => 'revision',
        ],
        'completed' => [
            'code' => 'completed',
            'name' => 'completed',
        ],
    ];

    public static function getTaskGroups()
    {
        return self::TASK_GROUPS;
    }
}
