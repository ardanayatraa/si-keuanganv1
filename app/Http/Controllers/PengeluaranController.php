<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\Anggaran;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter tanggal
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Pengeluaran::with(['kategori', 'rekening'])
            ->where('id_pengguna', Auth::user()->id_pengguna);

        if ($start) {
            $query->whereDate('tanggal', '>=', $start);
        }
        if ($end) {
            $query->whereDate('tanggal', '<=', $end);
        }

        $items = $query->orderBy('tanggal', 'desc')->get();

        return view('pengeluaran.index', compact('items', 'start', 'end'));
    }

    public function create()
    {
        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $kategoris = KategoriPengeluaran::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('pengeluaran.create', compact('rekenings', 'kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rekening'     => 'required|string|exists:rekening,id_rekening',
            'jumlah'          => 'required|numeric|min:0.01',
            'tanggal'         => 'required|date',
            'id_kategori'     => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'       => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
        ]);

        $data['id_pengguna'] = Auth::user()->id_pengguna;

        // Ambil data rekening
        $rekening = Rekening::where('id_rekening', $data['id_rekening'])->first();
        if ($rekening->saldo < $data['jumlah']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['jumlah' => 'Saldo rekening tidak mencukupi untuk melakukan pengeluaran ini.']);
        }

        // Simpan file bukti transaksi jika ada
        if ($request->hasFile('bukti_transaksi')) {
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')
                                              ->store('bukti_pengeluaran', 'public');
        }

        // Cek anggaran
        $warning = null;
        $anggaran = Anggaran::where('id_pengguna', $data['id_pengguna'])
            ->where('id_kategori', $data['id_kategori'])
            ->where('periode_awal', '<=', $data['tanggal'])
            ->where('periode_akhir', '>=', $data['tanggal'])
            ->first();

        if ($anggaran) {
            $totalSudah = Pengeluaran::where('id_pengguna', $data['id_pengguna'])
                ->where('id_kategori', $data['id_kategori'])
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal->toDateString(),
                    $anggaran->periode_akhir->toDateString(),
                ])
                ->sum('jumlah');

            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                $warning = "Peringatan: total pengeluaran periode {$anggaran->periode_awal->format('Y-m-d')}—{$anggaran->periode_akhir->format('Y-m-d')} sudah Rp " .
                           number_format($totalSudah,2,',','.') .
                           ". Dengan menambah Rp " . number_format($data['jumlah'],2,',','.') .
                           ", Anda akan melebihi batas Rp " . number_format($anggaran->jumlah_batas,2,',','.') . ".";
            }
        }

        // Simpan pengeluaran & update saldo dalam transaksi DB
        DB::transaction(function () use ($data) {
            Pengeluaran::create($data);
            Rekening::where('id_rekening', $data['id_rekening'])
                ->decrement('saldo', $data['jumlah']);
        });

        // Kirim warning ke session jika ada
        if ($warning) {
            session()->flash('warning', $warning);
        }

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dibuat.');
    }

    public function show($id)
    {
        $item = Pengeluaran::with(['kategori','rekening'])
            ->where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return view('pengeluaran.show', compact('item'));
    }

    public function edit($id)
    {
        $pengeluaran = Pengeluaran::with(['kategori', 'rekening'])
            ->where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $rekenings = Rekening::where('id_pengguna', Auth::user()->id_pengguna)->get();
        $kategoris = KategoriPengeluaran::where('id_pengguna', Auth::user()->id_pengguna)->get();

        return view('pengeluaran.edit', compact('pengeluaran', 'rekenings', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $data = $request->validate([
            'id_rekening'     => 'required|string|exists:rekening,id_rekening',
            'jumlah'          => 'required|numeric|min:0.01',
            'tanggal'         => 'required|date',
            'id_kategori'     => 'required|string|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'deskripsi'       => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
        ]);

        // Check if rekening has sufficient balance (only if different rekening or higher amount)
        if ($data['id_rekening'] != $pengeluaran->id_rekening || $data['jumlah'] > $pengeluaran->jumlah) {
            $additionalAmount = $data['id_rekening'] == $pengeluaran->id_rekening ?
                                $data['jumlah'] - $pengeluaran->jumlah :
                                $data['jumlah'];

            $rekening = Rekening::where('id_rekening', $data['id_rekening'])->first();
            if ($rekening->saldo < $additionalAmount) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['jumlah' => 'Saldo rekening tidak mencukupi untuk melakukan pengeluaran ini.']);
            }
        }

        if ($request->hasFile('bukti_transaksi')) {
            if ($pengeluaran->bukti_transaksi) {
                Storage::disk('public')->delete($pengeluaran->bukti_transaksi);
            }
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')
                                             ->store('bukti_pengeluaran', 'public');
        }

        // Cek anggaran (exclude current) dan prepare warning
        $warning = null;
        $anggaran = Anggaran::where('id_pengguna', $pengeluaran->id_pengguna)
            ->where('id_kategori', $data['id_kategori'])
            ->where('periode_awal', '<=', $data['tanggal'])
            ->where('periode_akhir', '>=', $data['tanggal'])
            ->first();

        if ($anggaran) {
            $totalSudah = Pengeluaran::where('id_pengguna', $pengeluaran->id_pengguna)
                ->where('id_kategori', $data['id_kategori'])
                ->whereBetween('tanggal', [
                    $anggaran->periode_awal->toDateString(),
                    $anggaran->periode_akhir->toDateString(),
                ])
                ->where('id_pengeluaran', '!=', $pengeluaran->id_pengeluaran)
                ->sum('jumlah');

            if (($totalSudah + $data['jumlah']) > $anggaran->jumlah_batas) {
                $warning = "Peringatan: total pengeluaran periode {$anggaran->periode_awal->format('Y-m-d')}—{$anggaran->periode_akhir->format('Y-m-d')} sudah Rp " .
                           number_format($totalSudah,2,',','.') .
                           ". Mengubah menjadi Rp " . number_format($data['jumlah'],2,',','.') .
                           " akan melebihi batas Rp " . number_format($anggaran->jumlah_batas,2,',','.') . ".";
            }
        }

        DB::transaction(function() use ($data, $pengeluaran) {
            // rollback saldo lama
            Rekening::where('id_rekening', $pengeluaran->id_rekening)
                   ->increment('saldo', $pengeluaran->jumlah);

            $pengeluaran->update($data);

            // kurangi saldo baru
            Rekening::where('id_rekening', $data['id_rekening'])
                   ->decrement('saldo', $data['jumlah']);
        });

        if ($warning) {
            session()->flash('warning', $warning);
        }

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        try {
            DB::transaction(function() use ($pengeluaran) {
                // restore saldo
                Rekening::where('id_rekening', $pengeluaran->id_rekening)
                       ->increment('saldo', $pengeluaran->jumlah);

                // hapus file bukti transaksi jika ada
                if ($pengeluaran->bukti_transaksi && Storage::disk('public')->exists($pengeluaran->bukti_transaksi)) {
                    Storage::disk('public')->delete($pengeluaran->bukti_transaksi);
                    Log::info('File bukti transaksi dihapus: ' . $pengeluaran->bukti_transaksi);
                }

                $pengeluaran->delete();
                Log::info('Pengeluaran berhasil dihapus ID: ' . $pengeluaran->id_pengeluaran);
            });
        } catch (\Exception $e) {
            Log::error('Error saat menghapus pengeluaran: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus pengeluaran: ' . $e->getMessage()]);
        }

        return redirect()->route('pengeluaran.index')
                         ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    /**
     * Method helper untuk menampilkan bukti transaksi
     */
    public function showBuktiTransaksi($id)
    {
        $pengeluaran = Pengeluaran::where('id_pengeluaran', $id)
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        if (!$pengeluaran->bukti_transaksi || !Storage::disk('public')->exists($pengeluaran->bukti_transaksi)) {
            abort(404, 'File bukti transaksi tidak ditemukan');
        }

        return response()->file(storage_path('app/public/' . $pengeluaran->bukti_transaksi));
    }
}
