<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadGroup extends Model
{
    use HasFactory, DianujHashidsTrait, SoftDeletes;

    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
