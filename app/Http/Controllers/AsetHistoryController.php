<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsetHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $asetId)
    {
        $aset = Aset::where('id_aset', $asetId)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        $history = $aset->history()->orderBy('tanggal_perubahan', 'desc')->get();

        return view('aset.history.index', compact('aset', 'history'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $asetId)
    {
        $aset = Aset::where('id_aset', $asetId)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        return view('aset.history.create', compact('aset'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $asetId)
    {
        $aset = Aset::where('id_aset', $asetId)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        $data = $request->validate([
            'nilai_baru' => 'required|numeric|min:0',
            'tanggal_perubahan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $data['id_aset'] = $aset->id_aset;
        $data['nilai_lama'] = $aset->nilai_aset;

        AsetHistory::create($data);

        // Update nilai aset
        $aset->update(['nilai_aset' => $data['nilai_baru']]);

        return redirect()->route('aset.show', $aset->id_aset)
                         ->with('success', 'Riwayat nilai aset berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $history = AsetHistory::findOrFail($id);

        // Pastikan history milik aset pengguna yang login
        $aset = Aset::where('id_aset', $history->id_aset)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        return view('aset.history.show', compact('history', 'aset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $history = AsetHistory::findOrFail($id);

        // Pastikan history milik aset pengguna yang login
        $aset = Aset::where('id_aset', $history->id_aset)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        return view('aset.history.edit', compact('history', 'aset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $history = AsetHistory::findOrFail($id);

        // Pastikan history milik aset pengguna yang login
        $aset = Aset::where('id_aset', $history->id_aset)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        $data = $request->validate([
            'tanggal_perubahan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $history->update($data);

        return redirect()->route('aset.history.index', $aset->id_aset)
                         ->with('success', 'Riwayat nilai aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $history = AsetHistory::findOrFail($id);

        // Pastikan history milik aset pengguna yang login
        $aset = Aset::where('id_aset', $history->id_aset)
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->firstOrFail();

        $history->delete();

        return redirect()->route('aset.history.index', $aset->id_aset)
                         ->with('success', 'Riwayat nilai aset berhasil dihapus.');
    }
}
