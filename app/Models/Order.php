<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'client_id',
        'order_code',
        'item_category_id',
        'fabric_color',
        'measurements',
        'design_image',
        'total_price',
        'deposit',
        'is_fully_paid',
        'status',
        'order_date',
        'delivery_date',
        'notes',
    ];

    protected $casts = [
        'measurements' => 'array',
        'is_fully_paid' => 'boolean',
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }
}
