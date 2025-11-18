<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Setting;
use Cart;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Menambahkan item ke keranjang (dengan support AJAX).
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        if (!session()->has('order_details')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan isi data pemesanan terlebih dahulu.',
                    'redirect' => route('order.start')
                ], 403);
            }
            return redirect()->route('order.start')
                ->with('error', 'Silakan isi data pemesanan terlebih dahulu.');
        }

        $product = Product::find($request->product_id);

        if (!$product) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $tableId = session('order_details.table_id');

        Cart::session($tableId)->add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $request->quantity,
            'attributes' => [
                'image' => $product->image,
            ]
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            // Hitung total items di cart
            $cartCount = Cart::session($tableId)->getTotalQuantity();
            $cartTotal = Cart::session($tableId)->getTotal();

            return response()->json([
                'success' => true,
                'message' => $product->name . ' ditambahkan ke keranjang!',
                'cart_count' => $cartCount,
                'cartTotalQuantity' => $cartCount, // Untuk kompatibilitas
                'cart_total' => number_format($cartTotal, 0, ',', '.'),
                'product_name' => $product->name
            ]);
        }

        return redirect()->route('order.menu')->with('success', 'Item berhasil ditambahkan ke keranjang!');
    }

    /**
     * Menampilkan halaman keranjang belanja dengan perhitungan subtotal, service fee, tax, grand total.
     */
    public function showCart(): View|RedirectResponse
    {
        if (!session()->has('order_details')) {
            return redirect()->route('order.start');
        }

        $tableId = session('order_details.table_id');

        $cartItems = Cart::session($tableId)->getContent()->sortBy('name');

        // SUBTOTAL
        $subtotal = Cart::session($tableId)->getTotal();

        // Ambil persentase dari database
        $service_percent = (float) (Setting::where('key', 'service_percent')->first()->value ?? 0);
        $tax_percent = (float) (Setting::where('key', 'tax_percent')->first()->value ?? 0);

        // HITUNG SERVICE FEE
        $service_fee_amount = round(($subtotal * $service_percent) / 100);

        // Pajak dihitung setelah service fee ditambah
        $tax_base = $subtotal + $service_fee_amount;
        $tax_amount = round(($tax_base * $tax_percent) / 100);

        // GRAND TOTAL
        $grand_total = $tax_base + $tax_amount;

        return view('cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'service_fee_amount' => $service_fee_amount,
            'tax_amount' => $tax_amount,
            'grand_total' => $grand_total,
            'service_percent' => $service_percent,
            'tax_percent' => $tax_percent,
        ]);
    }

    /**
     * Update kuantitas item di keranjang (Support AJAX).
     */
    public function update(Request $request, $id): JsonResponse|RedirectResponse
    {
        if (!session()->has('order_details')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak valid.'
                ], 403);
            }
            return redirect()->route('order.start');
        }

        $tableId = session('order_details.table_id');

        // Validasi quantity
        $quantity = $request->input('quantity', 1);
        if ($quantity < 1) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah minimal adalah 1.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Jumlah minimal adalah 1.');
        }

        try {
            Cart::session($tableId)->update($id, [
                'quantity' => [
                    'relative' => false,
                    'value' => $quantity
                ],
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                $cartCount = Cart::session($tableId)->getTotalQuantity();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Jumlah berhasil diperbarui.',
                    'cart_count' => $cartCount
                ]);
            }

            return redirect()->route('cart.index')
                ->with('success', 'Kuantitas berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui item.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal memperbarui item.');
        }
    }

    /**
     * Hapus 1 item dari keranjang (Support AJAX).
     */
    public function remove(Request $request, $id): JsonResponse|RedirectResponse
    {
        if (!session()->has('order_details')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak valid.'
                ], 403);
            }
            return redirect()->route('order.start');
        }

        $tableId = session('order_details.table_id');

        try {
            Cart::session($tableId)->remove($id);

            if ($request->ajax() || $request->wantsJson()) {
                $cartCount = Cart::session($tableId)->getTotalQuantity();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus.',
                    'cart_count' => $cartCount
                ]);
            }

            return redirect()->route('cart.index')
                ->with('success', 'Item berhasil dihapus dari keranjang.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus item.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal menghapus item.');
        }
    }

    /**
     * Get Cart Data untuk AJAX (Modal Cart).
     */
    public function getCartData(): JsonResponse
    {
        if (!session()->has('order_details')) {
            return response()->json([
                'items' => [], 
                'total_quantity' => 0, 
                'subtotal' => 0,
                'service_fee' => 0,
                'tax' => 0,
                'grand_total' => 0,
                'service_percent' => 0,
                'tax_percent' => 0
            ]);
        }

        $tableId = session('order_details.table_id');
        
        // Ambil Items
        $cartItems = Cart::session($tableId)->getContent()->sortBy('name');
        
        // Hitung Angka-angka (Sama seperti di OrderController)
        $subtotal = Cart::session($tableId)->getTotal();
        $totalQuantity = Cart::session($tableId)->getTotalQuantity();

        // Ambil setting pajak/layanan
        $service_percent = (float) (Setting::where('key', 'service_percent')->first()->value ?? 0);
        $tax_percent = (float) (Setting::where('key', 'tax_percent')->first()->value ?? 0);

        // Hitung
        $service_fee = round(($subtotal * $service_percent) / 100);
        $tax_base = $subtotal + $service_fee;
        $tax = round(($tax_base * $tax_percent) / 100);
        $grand_total = $tax_base + $tax;

        // Format data untuk dikirim ke JavaScript
        $formattedItems = [];
        foreach ($cartItems as $item) {
            $formattedItems[] = [
                'cart_id' => $item->id, // ID unik di cart (product_id)
                'id' => $item->id,      // ID produk
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $item->attributes->image ?? null,
                'subtotal' => $item->price * $item->quantity
            ];
        }

        return response()->json([
            'items' => $formattedItems,
            'total_quantity' => $totalQuantity,
            'subtotal' => $subtotal,
            'service_percent' => $service_percent,
            'service_fee' => $service_fee,
            'tax_percent' => $tax_percent,
            'tax' => $tax,
            'grand_total' => $grand_total
        ]);
    }
}