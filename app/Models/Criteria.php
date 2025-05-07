<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Criteria extends Model
{
    use HasFactory, DianujHashidsTrait, SoftDeletes;
}
