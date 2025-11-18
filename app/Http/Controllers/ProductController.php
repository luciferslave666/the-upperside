<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use App\Models\Setting;


class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk (menu).
     */
    public function index(): View
    {
        // 1. Ambil semua produk, urutkan dari yang terbaru
        $products = Product::with('category')->latest()->get();
        $tax = Setting::firstOrCreate(['key' => 'tax_percent'], ['value' => '11']);
        $service = Setting::firstOrCreate(['key' => 'service_percent'], ['value' => '5']);

        // 2. Kirim data produk ke view
        return view('admin.products.index', [
            'products' => $products,
            'tax' => $tax,
            'service' => $service,

        ]);
    }

    public function create(): View
    {
        // Ambil semua kategori untuk ditampilkan di dropdown form
        $categories = Category::all(); 
        
        return view('admin.products.create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request): RedirectResponse 
    {
        // 1. Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        // 2. Siapkan path gambar (default null)
        $imagePath = null;

        // 3. Cek jika ada file gambar yang di-upload
        if ($request->hasFile('image')) {
            // Simpan gambar di folder 'storage/app/public/products'
            // 'public' adalah disk yang kita link di Langkah 1
            $imagePath = $request->file('image')->store('products', 'public');
            
            // Masukkan path gambar ke data yang akan disimpan
            $validated['image'] = $imagePath;
        }

        // 4. Simpan data ke database
        Product::create($validated);

        // 5. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }
    public function edit(Product $product): View
    {
        // Ambil semua kategori untuk dropdown
        $categories = Category::all();

        // Kirim produk yang mau diedit DAN semua kategori ke view
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        // 1. Validasi input
        // 'image' tidak 'required', hanya divalidasi jika ada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Opsional
        ]);

        // 2. Cek jika ada file gambar baru
        if ($request->hasFile('image')) {
            
            // 3. Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // 4. Simpan gambar baru dan update path-nya
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // 5. Update data produk di database
        $product->update($validated);

        // 6. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
    }
    public function destroy(Product $product): RedirectResponse
    {
        // 1. Hapus gambar dari storage (jika ada)
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // 2. Hapus data produk dari database
        $product->delete();

        // 3. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}