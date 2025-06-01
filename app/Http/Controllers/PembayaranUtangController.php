<?php

namespace App\Http\Controllers;

use App\Models\PembayaranUtang;
use App\Models\Utang;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranUtangController extends Controller
{
    public function index()
    {
        $items = PembayaranUtang::with([
            'utang.pengguna',
            'pengeluaran.rekening'
        ])->get();

        return view('pembayaran-utang.index', compact('items'));
    }

    public function create()
    {
        // Kirim daftar utang belum lunas dan daftar rekening
        $utangs = Utang::where('sisa', '>', 0)->with('pengguna')->get();
        $rekenings = Rekening::all();

        return view('pembayaran-utang.create', compact('utangs', 'rekenings'));
    }

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
            // 1) Ambil utang untuk update sisa dan id_pengguna
            $utang = Utang::findOrFail($data['id_utang']);

            // 2) Buat record pengeluaran (kas keluar)
            $pengeluaran = Pengeluaran::create([
                'id_pengguna' => $utang->id_pengguna,
                'id_rekening' => $data['id_rekening'],
                'jumlah'      => $data['jumlah_dibayar'],
                'tanggal'     => $data['tanggal_pembayaran'],
                'id_kategori' => null, // atau kategori khusus “Pembayaran Utang”
                'deskripsi'   => 'Bayar Utang ID ' . $utang->id_utang,
            ]);

            // 3) Kurangi saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah_dibayar']);

            // 4) Update sisa utang dan status
            $utang->sisa -= $data['jumlah_dibayar'];
            if ($utang->sisa <= 0) {
                $utang->sisa = 0;
                $utang->status = 'lunas';
            }
            $utang->save();

            // 5) Simpan record pembayaran_utang
            PembayaranUtang::create([
                'id_utang'           => $data['id_utang'],
                'id_pengeluaran'     => $pengeluaran->id_pengeluaran,
                'id_rekening'        => $data['id_rekening'],
                'jumlah_dibayar'     => $data['jumlah_dibayar'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'metode_pembayaran'  => $data['metode_pembayaran'] ?? null,
                'deskripsi'          => $data['deskripsi'] ?? null,
            ]);
        });

        return redirect()
            ->route('pembayaran-utang.index')
            ->with('success', 'Pembayaran utang berhasil dibuat.');
    }

    public function show(PembayaranUtang $pembayaranUtang)
    {
        $pembayaranUtang->load(['utang.pengguna', 'pengeluaran.rekening']);
        return view('pembayaran-utang.show', compact('pembayaranUtang'));
    }

    public function edit(PembayaranUtang $pembayaranUtang)
    {
        $pembayaranUtang->load(['utang', 'pengeluaran.rekening']);
        $utangs = Utang::where('sisa', '>', 0)
                       ->orWhere('id_utang', $pembayaranUtang->id_utang)
                       ->with('pengguna')
                       ->get();
        $rekenings = Rekening::all();

        return view('pembayaran-utang.edit', compact('pembayaranUtang', 'utangs', 'rekenings'));
    }

    public function update(Request $request, PembayaranUtang $pembayaranUtang)
    {
        $data = $request->validate([
            'id_utang'           => 'required|exists:utang,id_utang',
            'id_rekening'        => 'required|exists:rekening,id_rekening',
            'jumlah_dibayar'     => 'required|numeric|min:0.01',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran'  => 'nullable|string',
            'deskripsi'          => 'nullable|string',
        ]);

        DB::transaction(function() use ($data, $pembayaranUtang) {
            // 1) Revert saldo dan sisa utang sebelumnya
            $oldPengeluaran = $pembayaranUtang->pengeluaran;
            $oldUtang       = $pembayaranUtang->utang;

            // Tambah kembali saldo rekening lama
            Rekening::where('id_rekening', $oldPengeluaran->id_rekening)
                   ->increment('saldo', $oldPengeluaran->jumlah);

            // Tambah kembali sisa utang
            $oldUtang->sisa += $pembayaranUtang->jumlah_dibayar;
            $oldUtang->status = $oldUtang->sisa > 0 ? 'belum lunas' : 'lunas';
            $oldUtang->save();

            // Hapus record pengeluaran lama
            $oldPengeluaran->delete();

            // 2) Buat pengeluaran baru dengan data terbaru
            $newPengeluaran = Pengeluaran::create([
                'id_pengguna' => $oldUtang->id_pengguna,
                'id_rekening' => $data['id_rekening'],
                'jumlah'      => $data['jumlah_dibayar'],
                'tanggal'     => $data['tanggal_pembayaran'],
                'id_kategori' => null,
                'deskripsi'   => 'Bayar Utang ID ' . $data['id_utang'],
            ]);

            // Kurangi saldo rekening baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah_dibayar']);

            // 3) Update sisa utang dan status pada utang yang dipilih
            $newUtang = Utang::findOrFail($data['id_utang']);
            $newUtang->sisa -= $data['jumlah_dibayar'];
            if ($newUtang->sisa <= 0) {
                $newUtang->sisa = 0;
                $newUtang->status = 'lunas';
            }
            $newUtang->save();

            // 4) Update pembayaran_utang
            $pembayaranUtang->update([
                'id_utang'           => $data['id_utang'],
                'id_pengeluaran'     => $newPengeluaran->id_pengeluaran,
                'id_rekening'        => $data['id_rekening'],
                'jumlah_dibayar'     => $data['jumlah_dibayar'],
                'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                'metode_pembayaran'  => $data['metode_pembayaran'] ?? null,
                'deskripsi'          => $data['deskripsi'] ?? null,
            ]);
        });

        return redirect()
            ->route('pembayaran-utang.index')
            ->with('success', 'Pembayaran utang berhasil diperbarui.');
    }

    public function destroy(PembayaranUtang $pembayaranUtang)
    {
        DB::transaction(function() use ($pembayaranUtang) {
            $utang       = $pembayaranUtang->utang;
            $pengeluaran = $pembayaranUtang->pengeluaran;

            // Kembalikan saldo rekening
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            // Kembalikan sisa utang
            $utang->sisa += $pembayaranUtang->jumlah_dibayar;
            $utang->status = $utang->sisa > 0 ? 'belum lunas' : 'lunas';
            $utang->save();

            // Hapus pengeluaran terkait
            $pengeluaran->delete();

            // Hapus record pembayaran_utang
            $pembayaranUtang->delete();
        });

        return redirect()
            ->route('pembayaran-utang.index')
            ->with('success', 'Pembayaran utang berhasil dihapus dan mutasi rekening dikembalikan.');
    }
}
