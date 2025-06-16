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
        // Validasi username, email, & password
        $data = $request->validate([
            'username' => 'required|string|max:50|unique:admin,username',
            'email'    => 'required|email|max:100|unique:admin,email',
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
        // Validasi username, email & (opsional) password
        $data = $request->validate([
            'username' => 'required|string|max:50|unique:admin,username,' . $admin->id_admin . ',id_admin',
            'email'    => 'required|email|max:100|unique:admin,email,' . $admin->id_admin . ',id_admin',
            'password' => 'nullable|string|min:6',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
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
