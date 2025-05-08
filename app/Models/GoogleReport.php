<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'act_id',
        'client_id',
        'campaign',
        'ads_group',
        'keywords',
        'ads',
        'summary_graph_data',
        'performance_graph_data',
        'performance_device',
        'start_date',
        'end_date',
        'last_update',
    ];
}
