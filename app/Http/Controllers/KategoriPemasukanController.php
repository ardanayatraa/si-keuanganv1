<?php

namespace App\Http\Controllers;

use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;

class KategoriPemasukanController extends Controller
{
    public function index()
    {
        $items = KategoriPemasukan::where('id_pengguna', auth()->user()->id_pengguna)
                                  ->orderBy('nama_kategori', 'asc')
                                  ->get();
        return view('kategori-pemasukan.index', compact('items'));
    }

    public function create()
    {
        return view('kategori-pemasukan.create');
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

        KategoriPemasukan::create($data);

        return redirect()->route('kategori-pemasukan.index')
                         ->with('success','Kategori Pemasukan berhasil dibuat.');
    }

    public function show(KategoriPemasukan $kategoriPemasukan)
    {
        return view('kategori-pemasukan.show', compact('kategoriPemasukan'));
    }

    public function edit(KategoriPemasukan $kategoriPemasukan)
    {
        return view('kategori-pemasukan.edit', compact('kategoriPemasukan'));
    }

    public function update(Request $request, KategoriPemasukan $kategoriPemasukan)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string',
            'deskripsi'     => 'nullable|string',
            'icon'          => 'nullable|string|max:100',
        ]);

        $kategoriPemasukan->update($data);

        return redirect()->route('kategori-pemasukan.index')
                         ->with('success','Kategori Pemasukan berhasil diperbarui.');
    }

    public function destroy(KategoriPemasukan $kategoriPemasukan)
    {
        $kategoriPemasukan->delete();

        return redirect()->route('kategori-pemasukan.index')
                         ->with('success','Kategori Pemasukan berhasil dihapus.');
    }
}
