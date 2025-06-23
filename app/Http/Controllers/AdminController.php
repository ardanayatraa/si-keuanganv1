<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    /**
     * Halaman Dashboard Admin:
     * - Menampilkan jumlah pengguna
     * - Menampilkan tabel pengguna dengan aksi CRUD
     */
    public function dashboard()
    {
        // Ambil semua pengguna
        $users = Pengguna::all();

        // Kirim ke view admin.dashboard (resources/views/admin/dashboard.blade.php)
        return view('admin.dashboard', compact('users'));
    }
}
