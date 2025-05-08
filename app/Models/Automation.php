<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Automation extends Model
{
    use HasFactory;

    protected $fillable = ['template_message_id', 'icon', 'name'];

    public function messageTemplate()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_message_id', 'id');
    }
}
