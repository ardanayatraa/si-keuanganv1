<?php

namespace App\Http\Controllers;

use App\Models\PembayaranUtang;
use App\Models\Utang;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranUtangController extends Controller
{
    /**
     * Tampilkan daftar pembayaran utang milik user login.
     */
    public function index()
    {
        $items = PembayaranUtang::with(['utang.pengguna', 'pengeluaran.rekening'])
            ->whereHas('utang', fn($q) => $q->where('id_pengguna', Auth::id()))
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();

        return view('utang.pembayaran.index', compact('items'));
    }

    /**
     * Form tambah pembayaran utang.
     */
    public function create()
    {
        // Eager-load relasi 'pembayaran' untuk hitung sudah bayar
        $utangs = Utang::with('pembayaran')
            ->where('id_pengguna', Auth::id())
            ->get();

        $rekenings = Rekening::where('id_pengguna', Auth::id())->get();

        return view('utang.pembayaran.create', compact('utangs', 'rekenings'));
    }

    /**
     * Simpan pembayaran utang baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_utang'           => 'required|exists:utang,id_utang',
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah_dibayar'     => 'required|numeric|min:0.01',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran'  => 'nullable|string',
            'deskripsi'          => 'nullable|string',
        ]);

        DB::transaction(function() use ($data) {
            $utang = Utang::where('id_utang', $data['id_utang'])
                          ->where('id_pengguna', Auth::id())
                          ->firstOrFail();

            // Pastikan kategori "Pembayaran Utang" ada
            $kat = KategoriPengeluaran::firstOrCreate(
                ['id_pengguna' => Auth::id(), 'nama_kategori' => 'Pembayaran Utang'],
                ['deskripsi' => 'Pembayaran utang', 'icon' => 'fas fa-credit-card']
            );

            // Buat record pengeluaran
            $pengeluaran = Pengeluaran::create([
                'id_pengguna' => $utang->id_pengguna,
                'id_rekening' => $data['id_rekening'],
                'jumlah'      => $data['jumlah_dibayar'],
                'tanggal'     => $data['tanggal_pembayaran'],
                'id_kategori' => $kat->id_kategori_pengeluaran,
                'deskripsi'   => 'Bayar Utang ID '.$utang->id_utang,
            ]);

            // Kurangi saldo rekening
            Rekening::find($data['id_rekening'])
                   ->decrement('saldo', $data['jumlah_dibayar']);

            // Catat pembayaran utang
            PembayaranUtang::create([
                'id_utang'           => $utang->id_utang,
                'id_pengeluaran'     => $pengeluaran->id_pengeluaran,
                'id_rekening'        => $data['id_rekening'],
                'jumlah_dibayar'     => $data['jumlah_dibayar'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'metode_pembayaran'  => $data['metode_pembayaran'] ?? null,
                'deskripsi'          => $data['deskripsi'] ?? null,
            ]);

            // Kurangi sisa hutang
            $utang->decrement('sisa_hutang', $data['jumlah_dibayar']);
            
            // Update status jika sudah lunas
            if ($utang->sisa_hutang <= 0) {
                $utang->update(['status' => 'lunas']);
            }
        });

        return redirect()
            ->route('utang.pembayaran.index')
            ->with('success', 'Pembayaran utang berhasil dibuat.');
    }

    /**
     * Tampilkan detail satu pembayaran.
     */
    public function show($id)
    {
        $p = PembayaranUtang::with(['utang.pengguna','pengeluaran.rekening'])
            ->where('id_pembayaran_utang', $id)
            ->whereHas('utang', fn($q) => $q->where('id_pengguna', Auth::id()))
            ->firstOrFail();

        return view('utang.pembayaran.show', ['pembayaran' => $p]);
    }

    /**
     * Form edit pembayaran utang.
     */
    public function edit($id)
    {
        $p = PembayaranUtang::with(['utang','pengeluaran.rekening'])
            ->where('id_pembayaran_utang', $id)
            ->whereHas('utang', fn($q) => $q->where('id_pengguna', Auth::id()))
            ->firstOrFail();

        // Eager-load 'pembayaran' agar opsi utang bisa hitung progress
        $utangs = Utang::with('pembayaran')
            ->where('id_pengguna', Auth::id())
            ->get();

        $rekenings = Rekening::where('id_pengguna', Auth::id())->get();

        return view('utang.pembayaran.edit', compact('p', 'utangs', 'rekenings'));
    }

    /**
     * Proses update pembayaran utang.
     */
    public function update(Request $request, $id)
    {
        $p = PembayaranUtang::where('id_pembayaran_utang', $id)
            ->whereHas('utang', fn($q) => $q->where('id_pengguna', Auth::id()))
            ->firstOrFail();

        $data = $request->validate([
            'id_utang'           => 'required|exists:utang,id_utang',
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah_dibayar'     => 'required|numeric|min:0.01',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran'  => 'nullable|string',
            'deskripsi'          => 'nullable|string',
        ]);

        DB::transaction(function() use ($data, $p) {
            // Rollback utang & mutasi lama
            $oldUtang   = $p->utang;
            $oldAmount  = $p->jumlah_dibayar;
            $oldUtang->increment('sisa_hutang', $oldAmount);
            
            // Update status kembali ke aktif jika sebelumnya lunas
            if ($oldUtang->status == 'lunas' && $oldUtang->sisa_hutang > 0) {
                $oldUtang->update(['status' => 'aktif']);
            }

            $oldPeng = $p->pengeluaran;
            Rekening::find($oldPeng->id_rekening)
                   ->increment('saldo', $oldPeng->jumlah);
            $oldPeng->delete();

            // Pastikan kategori
            $kat = KategoriPengeluaran::firstOrCreate(
                ['id_pengguna' => Auth::id(), 'nama_kategori' => 'Pembayaran Utang'],
                ['deskripsi'  => 'Pembayaran utang', 'icon' => 'fas fa-credit-card']
            );

            // Buat pengeluaran baru
            $newPeng = Pengeluaran::create([
                'id_pengguna' => $p->utang->id_pengguna,
                'id_rekening' => $data['id_rekening'],
                'jumlah'      => $data['jumlah_dibayar'],
                'tanggal'     => $data['tanggal_pembayaran'],
                'id_kategori' => $kat->id_kategori_pengeluaran,
                'deskripsi'   => 'Bayar Utang ID ' . $p->id_utang,
            ]);

            Rekening::find($data['id_rekening'])
                   ->decrement('saldo', $data['jumlah_dibayar']);

            // Update pembayaran
            $p->update([
                'id_utang'           => $data['id_utang'],
                'id_pengeluaran'     => $newPeng->id_pengeluaran,
                'id_rekening'        => $data['id_rekening'],
                'jumlah_dibayar'     => $data['jumlah_dibayar'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'metode_pembayaran'  => $data['metode_pembayaran'] ?? null,
                'deskripsi'          => $data['deskripsi'] ?? null,
            ]);

            // Kurangi utang baru
            $newUtang = $p->utang;
            $newUtang->decrement('sisa_hutang', $data['jumlah_dibayar']);
            
            // Update status jika sudah lunas
            if ($newUtang->sisa_hutang <= 0) {
                $newUtang->update(['status' => 'lunas']);
            }
        });

        return redirect()
            ->route('utang.pembayaran.index')
            ->with('success', 'Pembayaran utang berhasil diperbarui.');
    }

    /**
     * Hapus pembayaran utang.
     */
    public function destroy($id)
    {
        $p = PembayaranUtang::where('id_pembayaran_utang', $id)
            ->whereHas('utang', fn($q) => $q->where('id_pengguna', Auth::id()))
            ->firstOrFail();

        DB::transaction(function() use ($p) {
            // Rollback utang & mutasi
            $utang = $p->utang;
            $utang->increment('sisa_hutang', $p->jumlah_dibayar);
            
            // Update status kembali ke aktif jika sebelumnya lunas
            if ($utang->status == 'lunas' && $utang->sisa_hutang > 0) {
                $utang->update(['status' => 'aktif']);
            }

            $peng = $p->pengeluaran;
            Rekening::find($peng->id_rekening)
                   ->increment('saldo', $peng->jumlah);
            $peng->delete();

            $p->delete();
        });

        return redirect()
            ->route('utang.pembayaran.index')
            ->with('success', 'Pembayaran utang berhasil dihapus.');
    }
}
