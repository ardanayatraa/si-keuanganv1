<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\KategoriPemasukan;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $pengguna = Pengguna::create($data);

        // Create default categories for the new user
        $this->createDefaultCategories($pengguna->id_pengguna);

        return redirect()->route('admin.pengguna.index')
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

        return redirect()->route('admin.pengguna.index')
                         ->with('success','Pengguna berhasil diperbarui.');
    }

    public function destroy(Pengguna $pengguna)
    {
        // hapus file foto
        if ($pengguna->foto && Storage::disk('public')->exists($pengguna->foto)) {
            Storage::disk('public')->delete($pengguna->foto);
        }
        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')
                         ->with('success','Pengguna berhasil dihapus.');
    }


     /**
     * Form edit profil (data sendiri yang login).
     * GET /profile
     */
    public function editProfile()
   {
       $user = Auth::user();
       return view('pengguna.profile-edit', compact('user'));
   }

   /**
    * Proses update profil.
    * PUT /profile
    */
   public function updateProfile(Request $request)
   {
       $user = Auth::user();

        $data = $request->validate([
            'username' => 'required|string|max:50|unique:pengguna,username,'.$user->id_pengguna.',id_pengguna',
            'email'    => 'required|email|max:50|unique:pengguna,email,'.$user->id_pengguna.',id_pengguna',
            'password' => 'nullable|string|min:6',
            'saldo'    => 'required|numeric',
            'foto'     => 'nullable|image|max:2048',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('pengguna_foto', 'public');
        }

        $user->update($data);

        return redirect()->route('pengguna.profile.edit')
                         ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
 * Toggle antara 'aktif' <-> 'nonaktif'
 * @return \Illuminate\Http\JsonResponse
 */
public function toggleStatus(Request $request, Pengguna $pengguna)
{
    try {
        // Toggle status
        $newStatus = $pengguna->status === 'aktif' ? 'nonaktif' : 'aktif';
        $pengguna->status = $newStatus;
        $pengguna->save();

        // Log activity (optional)
        Log::info("Status pengguna {$pengguna->username} diubah menjadi {$newStatus} oleh admin: " . Auth::user()->username);

        return response()->json([
            'success' => true,
            'status' => $pengguna->status,
            'label'  => $pengguna->status === 'aktif' ? 'Aktif' : 'Nonaktif',
            'message' => "Status pengguna {$pengguna->username} berhasil diubah menjadi {$newStatus}"
        ]);

    } catch (\Exception $e) {
        Log::error("Error toggle status: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengubah status pengguna'
        ], 500);
    }
}

/**
 * Create default income and expense categories for a new user
 */
private function createDefaultCategories($userId)
{
    // Default income categories
    $incomeCategories = [
        ['nama_kategori' => 'Gaji', 'deskripsi' => 'Pendapatan dari pekerjaan tetap', 'icon' => 'money-bill'],
        ['nama_kategori' => 'Bonus', 'deskripsi' => 'Pendapatan tambahan dari pekerjaan', 'icon' => 'gift'],
        ['nama_kategori' => 'Investasi', 'deskripsi' => 'Pendapatan dari investasi', 'icon' => 'chart-line'],
        ['nama_kategori' => 'Bisnis', 'deskripsi' => 'Pendapatan dari usaha/bisnis', 'icon' => 'store'],
        ['nama_kategori' => 'Hadiah', 'deskripsi' => 'Pendapatan dari hadiah/pemberian', 'icon' => 'gift'],
        ['nama_kategori' => 'Penjualan', 'deskripsi' => 'Pendapatan dari penjualan barang', 'icon' => 'shopping-cart'],
        ['nama_kategori' => 'Dividen', 'deskripsi' => 'Pendapatan dari dividen saham', 'icon' => 'chart-pie'],
        ['nama_kategori' => 'Sewa', 'deskripsi' => 'Pendapatan dari menyewakan properti', 'icon' => 'home'],
        ['nama_kategori' => 'Royalti', 'deskripsi' => 'Pendapatan dari royalti', 'icon' => 'copyright'],
        ['nama_kategori' => 'Lain-lain', 'deskripsi' => 'Pendapatan dari sumber lainnya', 'icon' => 'ellipsis-h']
    ];

    // Default expense categories
    $expenseCategories = [
        ['nama_kategori' => 'Makanan', 'deskripsi' => 'Pengeluaran untuk makanan dan minuman', 'icon' => 'utensils'],
        ['nama_kategori' => 'Transportasi', 'deskripsi' => 'Pengeluaran untuk transportasi', 'icon' => 'car'],
        ['nama_kategori' => 'Belanja', 'deskripsi' => 'Pengeluaran untuk belanja', 'icon' => 'shopping-bag'],
        ['nama_kategori' => 'Hiburan', 'deskripsi' => 'Pengeluaran untuk hiburan', 'icon' => 'film'],
        ['nama_kategori' => 'Kesehatan', 'deskripsi' => 'Pengeluaran untuk kesehatan', 'icon' => 'medkit'],
        ['nama_kategori' => 'Pendidikan', 'deskripsi' => 'Pengeluaran untuk pendidikan', 'icon' => 'graduation-cap'],
        ['nama_kategori' => 'Tagihan', 'deskripsi' => 'Pengeluaran untuk tagihan rutin', 'icon' => 'file-invoice'],
        ['nama_kategori' => 'Rumah', 'deskripsi' => 'Pengeluaran untuk kebutuhan rumah', 'icon' => 'home'],
        ['nama_kategori' => 'Pakaian', 'deskripsi' => 'Pengeluaran untuk pakaian', 'icon' => 'tshirt'],
        ['nama_kategori' => 'Asuransi', 'deskripsi' => 'Pengeluaran untuk asuransi', 'icon' => 'shield-alt'],
        ['nama_kategori' => 'Donasi', 'deskripsi' => 'Pengeluaran untuk donasi/amal', 'icon' => 'hand-holding-heart'],
        ['nama_kategori' => 'Investasi', 'deskripsi' => 'Pengeluaran untuk investasi', 'icon' => 'chart-line'],
        ['nama_kategori' => 'Elektronik', 'deskripsi' => 'Pengeluaran untuk elektronik', 'icon' => 'laptop'],
        ['nama_kategori' => 'Olahraga', 'deskripsi' => 'Pengeluaran untuk olahraga', 'icon' => 'running'],
        ['nama_kategori' => 'Hewan Peliharaan', 'deskripsi' => 'Pengeluaran untuk hewan peliharaan', 'icon' => 'paw'],
        ['nama_kategori' => 'Kecantikan', 'deskripsi' => 'Pengeluaran untuk kecantikan/perawatan diri', 'icon' => 'spa'],
        ['nama_kategori' => 'Hadiah', 'deskripsi' => 'Pengeluaran untuk hadiah', 'icon' => 'gift'],
        ['nama_kategori' => 'Pajak', 'deskripsi' => 'Pengeluaran untuk pajak', 'icon' => 'file-invoice-dollar'],
        ['nama_kategori' => 'Liburan', 'deskripsi' => 'Pengeluaran untuk liburan', 'icon' => 'plane'],
        ['nama_kategori' => 'Lain-lain', 'deskripsi' => 'Pengeluaran untuk hal lainnya', 'icon' => 'ellipsis-h']
    ];

    // Create income categories
    foreach ($incomeCategories as $category) {
        KategoriPemasukan::create([
            'id_pengguna' => $userId,
            'nama_kategori' => $category['nama_kategori'],
            'deskripsi' => $category['deskripsi'],
            'icon' => $category['icon']
        ]);
    }

    // Create expense categories
    foreach ($expenseCategories as $category) {
        KategoriPengeluaran::create([
            'id_pengguna' => $userId,
            'nama_kategori' => $category['nama_kategori'],
            'deskripsi' => $category['deskripsi'],
            'icon' => $category['icon']
        ]);
    }
}
}
