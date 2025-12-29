<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id_order';
    public $incrementing = true; // ✅ Tambah ini

    protected $fillable = ['order_code', 'total_harga', 'status', 'snap_token'];

    // Relasi: 1 order punya banyak item
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }
}
