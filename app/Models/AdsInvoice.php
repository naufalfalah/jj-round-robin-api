<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsInvoice extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'client_id',
        'ads_id',
        'billing_id',
        'invoice_date',
        'card_charge',
        'gst',
        'total_amount',
        'total_lead',
        'start_date',
        'end_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->billing_id)) {
                $model->billing_id = generateRandomNumberString(12);
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
