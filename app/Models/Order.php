<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = ['order_code', 'total_harga', 'status', 'snap_token'];

    // Relasi: 1 order punya banyak item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
