<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'addedby_id',
        'template_id',
        'property',
        'generated_content',
        'converted_content',
        'audio_path',
    ];

    public function addedBy()
    {
        return $this->belongsTo(Admin::class, 'added_by_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo(PromptTemplate::class, 'template_id', 'id');
    }
}
