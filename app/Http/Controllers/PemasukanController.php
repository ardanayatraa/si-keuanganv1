<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    public function index()
    {
        // eager load relasi kategori, pengguna, rekening
        $items = Pemasukan::with('kategori', 'pengguna', 'rekening')->get();
        return view('pemasukan.index', compact('items'));
    }

    public function create()
    {
        // ambil semua rekening untuk dropdown
        $rekenings = Rekening::all();
        return view('pemasukan.create', compact('rekenings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // hapus id_pengguna dari validasi
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|max:50',
            'deskripsi'   => 'nullable|string',
        ]);

        // SET id_pengguna otomatis dari yang login
        $data['id_pengguna'] = auth()->user()->id_pengguna;

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

    public function show(Pemasukan $pemasukan)
    {
        return view('pemasukan.show', compact('pemasukan'));
    }

    public function edit(Pemasukan $pemasukan)
    {
        $rekenings = Rekening::all();
        return view('pemasukan.edit', compact('pemasukan', 'rekenings'));
    }

    public function update(Request $request, Pemasukan $pemasukan)
    {
        $data = $request->validate([
            'id_rekening' => 'required|string|exists:rekening,id_rekening',
            'jumlah'      => 'required|numeric|min:0.01',
            'tanggal'     => 'required|date',
            'id_kategori' => 'required|string|max:50',
            'deskripsi'   => 'nullable|string',
        ]);

        DB::transaction(function() use ($data, $pemasukan) {
            // 1) Rollback saldo lama
            Rekening::where('id_rekening', $pemasukan->id_rekening)
                   ->decrement('saldo', $pemasukan->jumlah);

            // 2) Update record pemasukan
            $pemasukan->update($data);

            // 3) Tambah saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function destroy(Pemasukan $pemasukan)
    {
        DB::transaction(function() use ($pemasukan) {
            // 1) Kurangi saldo sesuai jumlah yang dihapus
            Rekening::where('id_rekening', $pemasukan->id_rekening)
                   ->decrement('saldo', $pemasukan->jumlah);

            // 2) Hapus record pemasukan
            $pemasukan->delete();
        });

        return redirect()->route('pemasukan.index')
                         ->with('success', 'Pemasukan berhasil dihapus.');
    }
}
