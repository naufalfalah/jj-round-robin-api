<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'description', 'icon'];

    public function category()
    {
        return $this->belongsTo(CategoryTemplate::class, 'category_id', 'id');
    }
}
