<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // Tampilkan halaman toko / product
    public function index()
    {
        $products = Product::all();
        return view('shop', ['products' => $products]);
    }

    // Checkout
    public function checkout(Request $request)
    {
        // Ambil data keranjang dari FE
        $keranjangProducts = $request->item;

        // Hitung Total
        $total_harga = 0;
        foreach ($keranjangProducts as $item) {
            $product = Product::find($item['id_product']);
            $total_harga = $total_harga + ($product->harga * $item['jumlah']);
        }

        // Membuat kode Order
        $orderCode = 'ORDER-PRODCT-' . time();
        $order = Order::create([
            'order_code' => $orderCode,
            'total_harga' => $total_harga,
            'status' => 'pending'
        ]);

        // Simpan ke Order Item
        foreach ($keranjangProducts as $item) {
            $product = Product::find($item['id_product']);
            OrderItem::create([
                'id_order' => $order->id,
                'id_product' => $product->id,
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $product->harga
            ]);

            // Kurangi Stok
            $product->stok = $product->stok - $item['jumlah'];
            $product->save();
        }

        // Setup Data untuk Midtrans
        $midtransItems = [];
        foreach ($keranjangProducts as $item) {
            $product = Product::find($item['id_product']);
            $midtransItems[] = [
                'id' => $product->id_product,
                'price' => $product->harga,
                'quantity' => $item['jumlah'],
                'name' => $product->nama
            ];
        }

        // Setting Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;

        // Minta Token ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderCode,
                'gross_amount' => $total_harga,
            ],
            'item_details' => $midtransItems
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $order->snap_token = $snapToken;
        $order->save();

        // kirim kode token ke FE
        return response()->json([
            'token' => $snapToken,
            'order_code' => $orderCode,
        ]);
    }
    // Tangkep Notif dari Midtrans
    public function callback(Request $request)
    {
        // Setting Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        $notif = new \Midtrans\Notification;

        $status = $notif->transaction_status;
        $orderCode = $notif->order_id;

        $order = Order::where('order_code' . $orderCode)->first();

        if ($status === 'settlement' || $status === 'capture') {
            $order->status = 'success';
        } else if ($status === 'pending') {
            $order->status = 'pending';
        } else {
            $order->status = 'failed';
            // Kembalikan stok
            foreach ($order->item as $item) {
                $product = $item->product;
                $product->stok = $product->stok + $item->jumlah;
                $product->save();
            }
        }
        $order->save();
        return response()->json(['status' => 'ok']);
    }
}
