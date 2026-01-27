<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class OrderNote extends Model
{
    protected $fillable = ['order_id', 'note', 'user_id', 'is_customer_visible'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
