<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class KategoriPengeluaranController extends Controller
{
    public function index()
    {
        $items = KategoriPengeluaran::all();
        return view('kategori-pengeluaran.index', compact('items'));
    }

    public function create()
    {
        return view('kategori-pengeluaran.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string',
            'deskripsi'     => 'nullable|string',
            'icon'          => 'nullable|string|max:100',
        ]);

        // ambil id_pengguna dari yang login
        $data['id_pengguna'] = auth()->user()->id_pengguna;

        KategoriPengeluaran::create($data);

        return redirect()->route('kategori-pengeluaran.index')
                         ->with('success','Kategori Pengeluaran berhasil dibuat.');
    }

    public function show(KategoriPengeluaran $kategoriPengeluaran)
    {
        return view('kategori-pengeluaran.show', compact('kategoriPengeluaran'));
    }

    public function edit(KategoriPengeluaran $kategoriPengeluaran)
    {
        return view('kategori-pengeluaran.edit', compact('kategoriPengeluaran'));
    }

    public function update(Request $request, KategoriPengeluaran $kategoriPengeluaran)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string',
            'deskripsi'     => 'nullable|string',
            'icon'          => 'nullable|string|max:100',
        ]);

        $kategoriPengeluaran->update($data);

        return redirect()->route('kategori-pengeluaran.index')
                         ->with('success','Kategori Pengeluaran berhasil diperbarui.');
    }

    public function destroy(KategoriPengeluaran $kategoriPengeluaran)
    {
        $kategoriPengeluaran->delete();
        return redirect()->route('kategori-pengeluaran.index')
                         ->with('success','Kategori Pengeluaran berhasil dihapus.');
    }
}
