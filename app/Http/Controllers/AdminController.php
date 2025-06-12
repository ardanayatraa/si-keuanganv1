<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $items = Admin::all();
        return view('admin.index', compact('items'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        // Validasi hanya username & password
        $data = $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:6',
        ]);

        // Hash password sebelum simpan
        $data['password'] = bcrypt($data['password']);

        Admin::create($data);

        return redirect()->route('admin.index')
                         ->with('success', 'Admin berhasil dibuat.');
    }

    public function show(Admin $admin)
    {
        return view('admin.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        return view('admin.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        // Validasi hanya username & (opsional) password
        $data = $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        if (! empty($data['password'])) {
            // kalau ada password baru, hash dulu
            $data['password'] = bcrypt($data['password']);
        } else {
            // hapus key biar tidak overwrite field lama
            unset($data['password']);
        }

        $admin->update($data);

        return redirect()->route('admin.index')
                         ->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();

        return redirect()->route('admin.index')
                         ->with('success', 'Admin berhasil dihapus.');
    }
}
