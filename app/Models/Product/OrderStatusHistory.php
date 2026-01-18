<?php

namespace App\Models\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'old_status',
        'new_status',
        'note',
        'customer_notified',
    ];

    protected $casts = [
        'customer_notified' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// OrderNote Model
class OrderNote extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'note',
        'customer_visible',
    ];

    protected $casts = [
        'customer_visible' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
