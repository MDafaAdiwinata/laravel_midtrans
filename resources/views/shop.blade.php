<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout LarMid</title>

    @vite('resources/css/app.css')

    {{-- Midtrans Snap JS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-8">

        <!-- HEADER -->
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold">🛒 Toko Online</h1>
                <button onclick="showCart()" class="bg-blue-500 text-white px-6 py-3 rounded-lg">
                    Keranjang (<span id="cart-count">0</span>)
                </button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-8">

            <!-- PRODUK -->
            <div class="col-span-2">
                <h2 class="text-2xl font-bold mb-4">Produk</h2>
                <div class="grid grid-cols-2 gap-6">

                    @foreach ($products as $product)
                        <div class="bg-white p-4 rounded-lg shadow">
                            <img src="{{ $product->gambar }}" class="w-full h-48 object-cover rounded mb-3">
                            <h3 class="font-bold text-lg">{{ $product->nama }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ $product->deskripsi }}</p>
                            <p class="text-2xl font-bold text-blue-600 mb-2">Rp {{ number_format($product->harga) }}</p>
                            <p class="text-sm text-gray-500 mb-3">Stok: {{ $product->stok }}</p>

                            @if ($product->stok > 0)
                                <button
                                    onclick="addCart({{ $product->id_product }}, '{{ $product->nama }}', {{ $product->harga }}, {{ $product->stok }})"
                                    class="w-full bg-green-500 text-white py-2 rounded">
                                    Tambah
                                </button>
                            @else
                                <button disabled class="w-full bg-gray-300 text-gray-600 py-2 rounded">
                                    Habis
                                </button>
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- KERANJANG -->
            <div class="col-span-1">
                <div id="cart" class="bg-white p-6 rounded-lg shadow sticky top-8" style="display:none;">
                    <h2 class="text-2xl font-bold mb-4">Keranjang</h2>

                    <!-- Kosong -->
                    <div id="cart-empty" class="text-center py-8 text-gray-500">
                        Keranjang kosong
                    </div>

                    <!-- Isi -->
                    <div id="cart-items"></div>

                    <!-- Total -->
                    <div id="cart-footer" style="display:none;" class="mt-6 pt-6 border-t">
                        <div class="flex justify-between mb-4">
                            <span class="text-xl font-bold">Total:</span>
                            <span id="total" class="text-2xl font-bold text-blue-600">Rp 0</span>
                        </div>
                        <button onclick="pay()" class="w-full bg-blue-500 text-white py-3 rounded-lg font-bold">
                            Bayar
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // ============================================
        // DATA KERANJANG
        // ============================================
        let cart = [];

        // ============================================
        // FUNGSI: Tambah ke Keranjang
        // ============================================
        function addCart(id_product, nama, harga, stok) {
            let found = cart.find(item => item.id_product === id_product);

            if (found) {
                if (found.jumlah < found.stok) {
                    found.jumlah++;
                } else {
                    alert('Stok habis!');
                    return;
                }
            } else {
                cart.push({
                    id_product: id_product,
                    nama: nama,
                    harga_satuan: harga,
                    jumlah: 1,
                    stok: stok
                });
            }

            updateCart();
            showCart();
        }

        // ============================================
        // FUNGSI: Hapus dari Keranjang
        // ============================================
        function removeCart(id_product) {
            cart = cart.filter(item => item.id_product !== id_product);
            updateCart();
        }

        // ============================================
        // FUNGSI: Tambah Qty
        // ============================================
        function plusQty(id_product) {
            let item = cart.find(item => item.id_product === id_product);
            if (item.jumlah < item.stok) {
                item.jumlah++;
                updateCart();
            } else {
                alert('Stok habis!');
            }
        }

        // ============================================
        // FUNGSI: Kurang Qty
        // ============================================
        function minusQty(id_product) {
            let item = cart.find(item => item.id_product === id_product);
            if (item.jumlah > 1) {
                item.jumlah--;
                updateCart();
            }
        }

        // ============================================
        // FUNGSI: Update Tampilan Keranjang
        // ============================================
        function updateCart() {
            let count = cart.reduce((sum, item) => sum + item.jumlah, 0);
            document.getElementById('cart-count').textContent = count;

            if (cart.length === 0) {
                document.getElementById('cart-empty').style.display = 'block';
                document.getElementById('cart-items').innerHTML = '';
                document.getElementById('cart-footer').style.display = 'none';
            } else {
                document.getElementById('cart-empty').style.display = 'none';
                document.getElementById('cart-footer').style.display = 'block';

                let html = '';
                let total = 0;

                cart.forEach(item => {
                    let subtotal = item.harga_satuan * item.jumlah;
                    total += subtotal;

                    html += `
            <div class="mb-4 pb-4 border-b">
                <div class="flex justify-between mb-2">
                    <b>${item.nama}</b>
                    <button onclick="removeCart(${item.id_product})" class="text-red-500">×</button>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex gap-2">
                        <button onclick="minusQty(${item.id_product})" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span class="px-3 py-1">${item.jumlah}</span>
                        <button onclick="plusQty(${item.id_product})" class="bg-gray-200 px-3 py-1 rounded">+</button>
                    </div>
                    <b class="text-blue-600">Rp ${subtotal.toLocaleString('id-ID')}</b>
                </div>
            </div>
            `;
                });

                document.getElementById('cart-items').innerHTML = html;
                document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }
        }

        // ============================================
        // FUNGSI: Tampilkan Keranjang
        // ============================================
        function showCart() {
            document.getElementById('cart').style.display = 'block';
        }

        // ============================================
        // FUNGSI: BAYAR (PALING PENTING!)
        // ============================================
        async function pay() {
            if (cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }

            let items = cart.map(item => ({
                id_product: item.id_product,
                jumlah: item.jumlah
            }));

            let response = await fetch('/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    items
                })
            });

            let data = await response.json();

            snap.pay(data.token, {
                onSuccess: function() {
                    alert('Pembayaran Berhasil!');
                    cart = [];
                    updateCart();
                    location.reload();
                },
                onPending: function() {
                    alert('Menunggu pembayaran...');
                },
                onError: function() {
                    alert('Pembayaran Gagal!');
                }
            });
        }
    </script>

</body>

</html>
