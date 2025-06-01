<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        $items = Pengguna::all();
        return view('pengguna.index', compact('items'));
    }

    public function create()
    {
        return view('pengguna.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username'     => 'required|string|max:50',
            'email'        => 'required|email|max:50',
            'password'     => 'required|string|min:6',
        ]);

        Pengguna::create($data);
        return redirect()->route('pengguna.index')
                         ->with('success','Pengguna berhasil dibuat.');
    }

    public function show(Pengguna $pengguna)
    {
        return view('pengguna.show', compact('pengguna'));
    }

    public function edit(Pengguna $pengguna)
    {
        return view('pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $data = $request->validate([
            'username' => 'required|string|max:50',
            'email'    => 'required|email|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $pengguna->update($data);
        return redirect()->route('pengguna.index')
                         ->with('success','Pengguna berhasil diperbarui.');
    }

    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();
        return redirect()->route('pengguna.index')
                         ->with('success','Pengguna berhasil dihapus.');
    }
}
