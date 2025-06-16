<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Pengguna;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    public function index()
    {
        $items = Anggaran::with('kategori', 'pengguna')->get();
        return view('anggaran.index', compact('items'));
    }

    public function create()
    {
        // Form hanya butuh daftar kategori, pengguna diisi otomatis
        $listKategori = KategoriPengeluaran::all();
        return view('anggaran.create', compact('listKategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kategori'    => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'      => 'nullable|string',
            'jumlah_batas'   => 'required|numeric|min:0',
            'periode_awal'   => 'required|date',
            'periode_akhir'  => 'required|date|after_or_equal:periode_awal',
        ]);

        // isi id_pengguna otomatis
        $data['id_pengguna'] = auth()->user()->id_pengguna;

        Anggaran::create($data);

        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dibuat.');
    }

    public function show(Anggaran $anggaran)
    {
        return view('anggaran.show', compact('anggaran'));
    }

    public function edit(Anggaran $anggaran)
    {
        // Form edit hanya butuh kategori, pengguna tak diubah
        $listKategori = KategoriPengeluaran::all();
        return view('anggaran.edit', compact('anggaran', 'listKategori'));
    }

    public function update(Request $request, Anggaran $anggaran)
    {
        $data = $request->validate([
            'id_kategori'    => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'      => 'nullable|string',
            'jumlah_batas'   => 'required|numeric|min:0',
            'periode_awal'   => 'required|date',
            'periode_akhir'  => 'required|date|after_or_equal:periode_awal',
        ]);

        // tidak mengubah id_pengguna
        $anggaran->update($data);

        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroy(Anggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()
            ->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dihapus.');
    }
}
