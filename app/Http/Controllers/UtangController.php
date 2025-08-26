<?php

namespace App\Http\Controllers;

use App\Models\Utang;
use App\Models\Pemasukan;
use App\Models\Rekening;
use App\Models\KategoriPemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UtangController extends Controller
{
    /**
     * Daftar utang milik user login, dengan filter tanggal_pinjam.
     */
    public function index(Request $request)
    {
        // Ambil filter tanggal jika ada
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Utang::with(['pengguna', 'rekening'])
            ->where('id_pengguna', Auth::user()->id_pengguna);

        if ($start) {
            $query->whereDate('tanggal_pinjam', '>=', $start);
        }
        if ($end) {
            $query->whereDate('tanggal_pinjam', '<=', $end);
        }

        $items = $query
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();

        return view('utang.index', compact('items', 'start', 'end'));
    }

    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        return view('utang.create', compact('rekenings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'                => 'required|string|max:50',
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'jangka_waktu_bulan'  => 'nullable|integer|min:1',
            'deskripsi'           => 'nullable|string',
            'bukti_transaksi'     => 'nullable|image|max:2048',
        ]);

        // Hitung jumlah cicilan per bulan jika jangka waktu bulan diisi
        if (!empty($data['jangka_waktu_bulan'])) {
            $data['jumlah_cicilan_per_bulan'] = $data['jumlah'] / $data['jangka_waktu_bulan'];
        }

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // Proses upload file bukti transaksi jika ada
        $buktiTransaksi = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiTransaksi = $request->file('bukti_transaksi')
                                    ->store('bukti_pemasukan', 'public');
        }

        DB::transaction(function() use ($data, $buktiTransaksi) {
            // 1) Buat utang
            $utang = Utang::create([
                'nama'                => $data['nama'],
                'id_pengguna'         => $data['id_pengguna'],
                'id_rekening'         => $data['id_rekening'],
                'jumlah'              => $data['jumlah'],
                'tanggal_pinjam'      => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'sisa_hutang'         => $data['jumlah'],
                'jangka_waktu_bulan'  => $data['jangka_waktu_bulan'] ?? null,
                'jumlah_cicilan_per_bulan' => $data['jumlah_cicilan_per_bulan'] ?? null,
                'status'              => 'belum lunas',
            ]);

            // 2) Pastikan kategori "Utang" ada
            $kategoriUtang = KategoriPemasukan::firstOrCreate(
                ['id_pengguna' => $data['id_pengguna'], 'nama_kategori' => 'Utang'],
                ['deskripsi' => 'Kategori mencatat penerimaan utang', 'icon' => 'fas fa-hand-holding-usd']
            );

            // 3) Catat pemasukan dengan bukti transaksi
            $pemasukan = Pemasukan::create([
                'id_pengguna'     => $data['id_pengguna'],
                'jumlah'          => $data['jumlah'],
                'tanggal'         => $data['tanggal_pinjam'],
                'id_kategori'     => $kategoriUtang->id_kategori_pemasukan,
                'deskripsi'       => 'Terima utang (ID ' . $utang->id_utang . ')',
                'id_rekening'     => $data['id_rekening'],
                'bukti_transaksi' => $buktiTransaksi,
            ]);

            // 4) Update saldo rekening
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('utang.index')
                         ->with('success', 'Utang berhasil dicatat dan saldo rekening bertambah.');
    }

    public function show($id)
    {
        $utang = Utang::with(['pengguna','rekening'])
            ->where('id_utang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return view('utang.show', compact('utang'));
    }

    public function edit($id)
    {
        $utang = Utang::where('id_utang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('utang.edit', compact('utang','rekenings'));
    }

    public function update(Request $request, $id)
    {
        $utang = Utang::where('id_utang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'nama'                => 'required|string|max:50',
            'id_rekening'         => 'required|exists:rekening,id_rekening',
            'jumlah'              => 'required|numeric|min:0.01',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'jangka_waktu_bulan'  => 'nullable|integer|min:1',
            'deskripsi'           => 'nullable|string',
            'bukti_transaksi'     => 'nullable|image|max:2048',
        ]);

        // Hitung jumlah cicilan per bulan jika jangka waktu bulan diisi
        if (!empty($data['jangka_waktu_bulan'])) {
            $data['jumlah_cicilan_per_bulan'] = $data['jumlah'] / $data['jangka_waktu_bulan'];
        }

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // Proses upload file bukti transaksi jika ada
        $buktiTransaksi = null;
        if ($request->hasFile('bukti_transaksi')) {
            // Hapus file lama jika ada
            $pemasukanLama = Pemasukan::where('deskripsi', 'like', '%Terima utang (ID ' . $utang->id_utang . ')%')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->first();

            if ($pemasukanLama && $pemasukanLama->bukti_transaksi) {
                Storage::disk('public')->delete($pemasukanLama->bukti_transaksi);
            }

            $buktiTransaksi = $request->file('bukti_transaksi')
                                    ->store('bukti_pemasukan', 'public');
        } else {
            // Jika tidak ada file baru, pertahankan file lama
            $pemasukanLama = Pemasukan::where('deskripsi', 'like', '%Terima utang (ID ' . $utang->id_utang . ')%')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->first();

            if ($pemasukanLama) {
                $buktiTransaksi = $pemasukanLama->bukti_transaksi;
            }
        }

        DB::transaction(function() use ($data, $utang, $buktiTransaksi) {
            // Cari pemasukan yang terkait dengan utang ini
            $pemasukanLama = Pemasukan::where('deskripsi', 'like', '%Terima utang (ID ' . $utang->id_utang . ')%')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->first();

            // refund saldo lama
            Rekening::where('id_rekening', $utang->id_rekening)
                   ->decrement('saldo', $utang->jumlah);

            // update utang
            $utang->update([
                'nama'                => $data['nama'],
                'id_rekening'         => $data['id_rekening'],
                'jumlah'              => $data['jumlah'],
                'sisa_hutang'         => $data['jumlah'],
                'tanggal_pinjam'      => $data['tanggal_pinjam'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'jangka_waktu_bulan'  => $data['jangka_waktu_bulan'] ?? null,
                'jumlah_cicilan_per_bulan' => $data['jumlah_cicilan_per_bulan'] ?? null,
                'deskripsi'           => $data['deskripsi'] ?? null,
                'status'              => 'belum lunas',
            ]);

            // pastikan kategori Utang
            $kategoriUtang = KategoriPemasukan::firstOrCreate(
                ['id_pengguna' => $data['id_pengguna'], 'nama_kategori' => 'Utang'],
                ['deskripsi' => 'Kategori mencatat penerimaan utang', 'icon' => 'fas fa-hand-holding-usd']
            );

            // Update pemasukan yang sudah ada (jangan buat baru)
            if ($pemasukanLama) {
                $pemasukanLama->update([
                    'jumlah'          => $data['jumlah'],
                    'tanggal'         => $data['tanggal_pinjam'],
                    'id_kategori'     => $kategoriUtang->id_kategori_pemasukan,
                    'deskripsi'       => 'Terima utang (ID ' . $utang->id_utang . ')',
                    'id_rekening'     => $data['id_rekening'],
                    'bukti_transaksi' => $buktiTransaksi,
                ]);
            } else {
                // Jika tidak ada pemasukan lama, buat baru
                Pemasukan::create([
                    'id_pengguna'     => $data['id_pengguna'],
                    'jumlah'          => $data['jumlah'],
                    'tanggal'         => $data['tanggal_pinjam'],
                    'id_kategori'     => $kategoriUtang->id_kategori_pemasukan,
                    'deskripsi'       => 'Terima utang (ID ' . $utang->id_utang . ')',
                    'id_rekening'     => $data['id_rekening'],
                    'bukti_transaksi' => $buktiTransaksi,
                ]);
            }

            // update saldo rekening baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->increment('saldo', $data['jumlah']);
        });

        return redirect()->route('utang.index')
                         ->with('success', 'Utang berhasil diperbarui dan mutasi rekening disesuaikan.');
    }

    public function destroy($id)
    {
        $utang = Utang::where('id_utang', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        DB::transaction(function() use ($utang) {
            // Hapus file bukti transaksi jika ada
            $pemasukan = Pemasukan::where('deskripsi', 'like', '%Terima utang (ID ' . $utang->id_utang . ')%')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->first();

            if ($pemasukan && $pemasukan->bukti_transaksi) {
                Storage::disk('public')->delete($pemasukan->bukti_transaksi);
            }

            // hapus pemasukan & refund saldo
            Pemasukan::where('deskripsi', 'like', '%Terima utang (ID ' . $utang->id_utang . ')%')
                     ->where('tanggal', $utang->tanggal_pinjam)
                     ->where('jumlah', $utang->jumlah)
                     ->where('id_rekening', $utang->id_rekening)
                     ->delete();
            Rekening::where('id_rekening', $utang->id_rekening)
                   ->decrement('saldo', $utang->jumlah);

            // hapus utang
            $utang->delete();
        });

        return redirect()->route('utang.index')
                         ->with('success', 'Utang berhasil dihapus dan saldo rekening dikembalikan.');
    }
    public function getInstallments($utangId)
    {
        $utang = Utang::with('jadwalCicilanUtang')->find($utangId);

        if (!$utang) {
            return response()->json(['message' => 'Utang not found'], 404);
        }

        $installments = $utang->jadwalCicilanUtang->map(function ($cicilan) {
            return [
                'tanggal_jatuh_tempo' => $cicilan->tanggal_jatuh_tempo,
                'jumlah_cicilan' => $cicilan->jumlah_cicilan,
                'status' => $cicilan->status,
            ];
        });

        return response()->json($installments);
    }
}
