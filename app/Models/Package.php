<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_account_id',
        'name',
        'description',
        'price',
        'url',
    ];

    public function sub_account()
    {
        return $this->belongsTo(SubAccount::class, 'sub_account_id');
    }

    public function menus()
    {
        return $this->hasMany(PackageMenu::class);
    }

    public function getMenus(): array
    {
        return $this->menus()->pluck('menu')->toArray();
    }
}
