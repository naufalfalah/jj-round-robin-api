<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadClientsExport implements FromArray, WithHeadings
{
    protected $leadData;

    public function __construct(array $leadData)
    {
        $this->leadData = $leadData;
    }

    public function array(): array
    {
        return $this->leadData;
    }

    public function headings(): array
    {
        return [
            'Client Name',
            'Name',
            'Email',
            'Phone',
            'Additional Data',
            'Lead Date & Time',
            'Status',
            'For Call Status'
        ];
    }
}
