<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function index()
    {
        $items = Rekening::with('pengguna')->get();
        return view('rekening.index', compact('items'));
    }

    public function create()
    {
        return view('rekening.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pengguna'   => 'required|string|max:50',
            'nama_rekening' => 'required|string|max:50',
            'saldo'         => 'required|numeric',
        ]);

        Rekening::create($data);
        return redirect()->route('rekening.index')
                         ->with('success','Rekening berhasil dibuat.');
    }

    public function show(Rekening $rekening)
    {
        return view('rekening.show', compact('rekening'));
    }

    public function edit(Rekening $rekening)
    {
        return view('rekening.edit', compact('rekening'));
    }

    public function update(Request $request, Rekening $rekening)
    {
        $data = $request->validate([
            'nama_rekening' => 'required|string|max:50',
            'saldo'         => 'required|numeric',
        ]);

        $rekening->update($data);
        return redirect()->route('rekening.index')
                         ->with('success','Rekening berhasil diperbarui.');
    }

    public function destroy(Rekening $rekening)
    {
        $rekening->delete();
        return redirect()->route('rekening.index')
                         ->with('success','Rekening berhasil dihapus.');
    }
}
