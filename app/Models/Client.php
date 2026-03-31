<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'phone', 'is_traveler', 'image'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
