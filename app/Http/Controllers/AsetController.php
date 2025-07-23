<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Aset::where('id_pengguna', Auth::user()->id_pengguna)
                     ->get();

        $totalNilai = $items->where('status', 'aktif')->sum('nilai_aset');

        return view('aset.index', compact('items', 'totalNilai'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('aset.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_aset' => 'required|string|max:100',
            'jenis_aset' => 'required|in:Tunai/Rekening Bank,Properti,Kendaraan,Elektronik,Investasi,Aset Digital,Lain-lain',
            'nilai_aset' => 'required|numeric|min:0',
            'tanggal_perolehan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;
        $data['status'] = 'aktif';

        $aset = Aset::create($data);

        return redirect()->route('aset.index')
                         ->with('success', 'Aset berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $aset = Aset::where('id_aset', $id)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        $history = $aset->history()->orderBy('tanggal_perubahan', 'desc')->get();

        return view('aset.show', compact('aset', 'history'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aset = Aset::where('id_aset', $id)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        return view('aset.edit', compact('aset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $aset = Aset::where('id_aset', $id)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        $data = $request->validate([
            'nama_aset' => 'required|string|max:100',
            'jenis_aset' => 'required|in:Tunai/Rekening Bank,Properti,Kendaraan,Elektronik,Investasi,Aset Digital,Lain-lain',
            'nilai_aset' => 'required|numeric|min:0',
            'tanggal_perolehan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        // Jika nilai aset berubah, catat dalam history
        if ($aset->nilai_aset != $data['nilai_aset']) {
            AsetHistory::create([
                'id_aset' => $aset->id_aset,
                'nilai_lama' => $aset->nilai_aset,
                'nilai_baru' => $data['nilai_aset'],
                'tanggal_perubahan' => now()->format('Y-m-d'),
                'keterangan' => $request->input('alasan_perubahan', 'Perubahan nilai aset')
            ]);
        }

        $aset->update($data);

        return redirect()->route('aset.index')
                         ->with('success', 'Aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $aset = Aset::where('id_aset', $id)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        // Hapus history aset terlebih dahulu
        $aset->history()->delete();

        // Kemudian hapus aset
        $aset->delete();

        return redirect()->route('aset.index')
                         ->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Ubah status aset (aktif/terjual)
     */
    public function toggleStatus(string $id)
    {
        $aset = Aset::where('id_aset', $id)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        $newStatus = $aset->status === 'aktif' ? 'terjual' : 'aktif';

        $aset->update(['status' => $newStatus]);

        $message = $newStatus === 'terjual'
                 ? 'Aset berhasil ditandai sebagai terjual.'
                 : 'Aset berhasil diaktifkan kembali.';

        return redirect()->route('aset.index')
                         ->with('success', $message);
    }

    /**
     * Menampilkan total nilai kekayaan
     */
    public function totalWealth()
    {
        $totalAset = Aset::where('id_pengguna', Auth::user()->id_pengguna)
                         ->where('status', 'aktif')
                         ->sum('nilai_aset');

        return view('aset.total-wealth', compact('totalAset'));
    }
}
