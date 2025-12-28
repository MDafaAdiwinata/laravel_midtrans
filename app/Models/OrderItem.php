<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = "order_items";
    protected $primaryKey = 'id_order_item';

    protected $fillable = [
        'id_order',
        'id_product',
        'jumlah',
        'harga_satuan',
    ];

    // relasi item order ini punya 1 product
    public function pruduct()
    {
        return $this->belongsTo(Product::class);
    }
}
