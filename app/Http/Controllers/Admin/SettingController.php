<?php
// File: app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; // <-- Import model
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan.
     */
    public function index(): View
    {
        // Gunakan firstOrCreate() untuk memastikan data ada.
        // Jika 'tax_percent' belum ada, buat baru dengan nilai '11' (11%).
        $tax = Setting::firstOrCreate(
            ['key' => 'tax_percent'],
            ['value' => '11'] 
        );

        // Jika 'service_percent' belum ada, buat baru dengan nilai '5' (5%).
        $service = Setting::firstOrCreate(
            ['key' => 'service_percent'],
            ['value' => '5']
        );

        return view('admin.settings.index', [
            'tax' => $tax,
            'service' => $service,
        ]);
    }

    /**
     * Memperbarui pengaturan di database.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Validasi input
        $validated = $request->validate([
            'tax_percent' => 'required|numeric|min:0|max:100',
            'service_percent' => 'required|numeric|min:0|max:100',
        ]);

        // 2. Gunakan updateOrCreate() untuk menyimpan pengaturan
        // (Ini akan meng-update jika 'key' sudah ada, atau membuat baru jika belum)
        Setting::updateOrCreate(
            ['key' => 'tax_percent'],
            ['value' => $validated['tax_percent']]
        );

        Setting::updateOrCreate(
            ['key' => 'service_percent'],
            ['value' => $validated['service_percent']]
        );

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.products.index')->with('success', 'Pengaturan pajak & layanan berhasil disimpan!');
    }
}