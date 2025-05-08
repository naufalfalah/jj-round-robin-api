<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    public function group_leads()
    {
        return $this->hasMany(LeadGroup::class, 'group_id');
    }
}
