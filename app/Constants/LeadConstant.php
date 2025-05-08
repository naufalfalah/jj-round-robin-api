<?php

namespace App\Constants;

class LeadConstant
{
    public const FILTERS = [
        'junk' => [
            'label' => 'Junk',
            'value' => 'junk',
        ],
        'duplicate' => [
            'label' => 'Duplicate',
            'value' => 'duplicate',
        ],
        'dnc' => [
            'label' => 'DNC',
            'value' => 'dnc',
        ],
        'bad_words' => [
            'label' => 'Bad Words',
            'value' => 'bad_words',
        ],
    ];

    public static function getFilters(): array
    {
        return array_keys(self::FILTERS);
    }
}
