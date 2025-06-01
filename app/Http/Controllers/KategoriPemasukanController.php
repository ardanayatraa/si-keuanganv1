<?php

namespace App\Http\Controllers;

use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;

class KategoriPemasukanController extends Controller
{
    public function index()
    {
        $items = KategoriPemasukan::all();
        return view('kategori-pemasukan.index', compact('items'));
    }

    public function create()
    {
        return view('kategori-pemasukan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pengguna'           => 'required|string|max:50',
            'nama_kategori'         => 'required|string',
            'deskripsi'             => 'nullable|string',
            'icon'                  => 'nullable|string|max:100',
        ]);

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
