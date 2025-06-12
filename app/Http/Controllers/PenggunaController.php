<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'username' => 'required|string|max:50',
            'email'    => 'required|email|max:50',
            'password' => 'required|string|min:6',
            'saldo'    => 'numeric',
            'foto'     => 'nullable|image|max:2048', // jpg/png, max 2MB
        ]);

        // hash password
        $data['password'] = bcrypt($data['password']);

        // handle upload foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('pengguna_foto', 'public');
            $data['foto'] = $path;
        }

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
            'saldo'    => 'required|numeric',
            'foto'     => 'nullable|image|max:2048',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // handle upload foto baru
        if ($request->hasFile('foto')) {
            // hapus foto lama jika ada
            if ($pengguna->foto && Storage::disk('public')->exists($pengguna->foto)) {
                Storage::disk('public')->delete($pengguna->foto);
            }
            $path = $request->file('foto')->store('pengguna_foto', 'public');
            $data['foto'] = $path;
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')
                         ->with('success','Pengguna berhasil diperbarui.');
    }

    public function destroy(Pengguna $pengguna)
    {
        // hapus file foto
        if ($pengguna->foto && Storage::disk('public')->exists($pengguna->foto)) {
            Storage::disk('public')->delete($pengguna->foto);
        }
        $pengguna->delete();

        return redirect()->route('pengguna.index')
                         ->with('success','Pengguna berhasil dihapus.');
    }
}
