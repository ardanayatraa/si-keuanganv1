<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    /**
     * Tampilkan daftar pemasukan milik user login.
     */
    public function index()
    {
        $items = Pemasukan::with('kategori', 'rekening')
                    ->where('id_pengguna', Auth::user()->id_pengguna)
                    ->orderBy('tanggal', 'desc')
                    ->get();

        return view('pemasukan.index', compact('items'));
    }

    /**
     * Form tambah pemasukan. Rekening hanya milik user.
     */
    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        return view('pemasukan.create', compact('rekenings'));
    }

    /**
     * Simpan pemasukan baru untuk user login.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|max:50',
            'deskripsi'   => 'nullable|string',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        DB::transaction(function() use ($data) {
            // 1) Buat record pemasukan
            $p = Pemasukan::create($data);

            // 2) Tambah saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil dibuat.');
    }

    /**
     * Detail pemasukan (pastikan milik user).
     */
    public function show($id)
    {
        $pemasukan = Pemasukan::with('kategori', 'rekening')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->findOrFail($id);

        return view('pemasukan.show', compact('pemasukan'));
    }

    /**
     * Form edit pemasukan (pastikan milik user).
     */
    public function edit($id)
    {
        $pemasukan = Pemasukan::where('id_pengguna', Auth::user()->id_pengguna)
            ->findOrFail($id);

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('pemasukan.edit', compact('pemasukan', 'rekenings'));
    }

    /**
     * Update pemasukan (pastikan milik user).
     */
    public function update(Request $request, $id)
    {
        $pemasukan = Pemasukan::where('id_pengguna', Auth::user()->id_pengguna)
            ->findOrFail($id);

        $data = $request->validate([
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|max:50',
            'deskripsi'   => 'nullable|string',
        ]);

        DB::transaction(function() use ($data, $pemasukan) {
            // rollback saldo lama
            Rekening::where('id_rekening', $pemasukan->id_rekening)
                   ->decrement('saldo', $pemasukan->jumlah);

            // update record
            $pemasukan->update($data);

            // tambah saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil diperbarui.');
    }

    /**
     * Hapus pemasukan (pastikan milik user).
     */
    public function destroy($id)
    {
        $pemasukan = Pemasukan::where('id_pengguna', Auth::user()->id_pengguna)
            ->findOrFail($id);

        DB::transaction(function() use ($pemasukan) {
            // kurangi saldo
            Rekening::where('id_rekening', $pemasukan->id_rekening)
                   ->decrement('saldo', $pemasukan->jumlah);

            $pemasukan->delete();
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil dihapus.');
    }
}
