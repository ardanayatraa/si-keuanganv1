<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Wishlist::where('id_pengguna', Auth::user()->id_pengguna)
                         ->orderBy('status')
                         ->orderBy('tanggal_target')
                         ->get();

        // Group items by category
        $itemsByCategory = $items->groupBy('kategori');

        // Calculate total estimated cost and collected funds
        $totalEstimasi = $items->where('status', 'pending')->sum('estimasi_harga');
        $totalTerkumpul = $items->where('status', 'pending')->sum('dana_terkumpul');

        return view('wishlist.index', compact('items', 'itemsByCategory', 'totalEstimasi', 'totalTerkumpul'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Define common categories
        $categories = [
            'Elektronik',
            'Travel',
            'Pendidikan',
            'Kendaraan',
            'Properti',
            'Investasi',
            'Lain-lain'
        ];

        return view('wishlist.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_item' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'estimasi_harga' => 'required|numeric|min:0',
            'tanggal_target' => 'required|date',
            'dana_terkumpul' => 'nullable|numeric|min:0',
            'sumber_dana' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;
        $data['status'] = 'pending';

        // If dana_terkumpul is not provided, set it to 0
        if (!isset($data['dana_terkumpul'])) {
            $data['dana_terkumpul'] = 0;
        }

        Wishlist::create($data);

        return redirect()->route('wishlist.index')
                         ->with('success', 'Item wishlist berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Wishlist::where('id_wishlist', $id)
                        ->where('id_pengguna', Auth::user()->id_pengguna)
                        ->firstOrFail();

        return view('wishlist.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Wishlist::where('id_wishlist', $id)
                        ->where('id_pengguna', Auth::user()->id_pengguna)
                        ->firstOrFail();

        // Define common categories
        $categories = [
            'Elektronik',
            'Travel',
            'Pendidikan',
            'Kendaraan',
            'Properti',
            'Investasi',
            'Lain-lain'
        ];

        return view('wishlist.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Wishlist::where('id_wishlist', $id)
                        ->where('id_pengguna', Auth::user()->id_pengguna)
                        ->firstOrFail();

        $data = $request->validate([
            'nama_item' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'estimasi_harga' => 'required|numeric|min:0',
            'tanggal_target' => 'required|date',
            'dana_terkumpul' => 'nullable|numeric|min:0',
            'sumber_dana' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        // If dana_terkumpul is not provided, keep the existing value
        if (!isset($data['dana_terkumpul'])) {
            $data['dana_terkumpul'] = $item->dana_terkumpul;
        }

        $item->update($data);

        return redirect()->route('wishlist.index')
                         ->with('success', 'Item wishlist berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Wishlist::where('id_wishlist', $id)
                        ->where('id_pengguna', Auth::user()->id_pengguna)
                        ->firstOrFail();

        $item->delete();

        return redirect()->route('wishlist.index')
                         ->with('success', 'Item wishlist berhasil dihapus.');
    }

    /**
     * Update progress (dana terkumpul) for a wishlist item
     */
    public function updateProgress(Request $request, string $id)
    {
        $item = Wishlist::where('id_wishlist', $id)
                        ->where('id_pengguna', Auth::user()->id_pengguna)
                        ->firstOrFail();

        $data = $request->validate([
            'dana_terkumpul' => 'required|numeric|min:0',
        ]);

        $item->update($data);

        // If dana_terkumpul >= estimasi_harga, mark as achieved
        if ($item->dana_terkumpul >= $item->estimasi_harga) {
            $item->update(['status' => 'tercapai']);
        }

        return redirect()->route('wishlist.show', $item->id_wishlist)
                         ->with('success', 'Progress wishlist berhasil diperbarui.');
    }

    /**
     * Toggle status (pending/tercapai) for a wishlist item
     */
    public function toggleStatus(string $id)
    {
        $item = Wishlist::where('id_wishlist', $id)
                        ->where('id_pengguna', Auth::user()->id_pengguna)
                        ->firstOrFail();

        $newStatus = $item->status === 'pending' ? 'tercapai' : 'pending';

        $item->update(['status' => $newStatus]);

        $message = $newStatus === 'tercapai'
                 ? 'Item wishlist berhasil ditandai sebagai tercapai.'
                 : 'Item wishlist berhasil dikembalikan ke status pending.';

        return redirect()->route('wishlist.index')
                         ->with('success', $message);
    }
}
