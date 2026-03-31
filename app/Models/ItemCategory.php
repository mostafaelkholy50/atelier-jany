<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $fillable = ['name', 'default_measurements'];

    protected $casts = [
        'default_measurements' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
