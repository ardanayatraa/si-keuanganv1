<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Tampilkan form + list laporan.
     */
public function index()
{
    // ambil 10 per halaman, hanya laporan milik user yang login, eager-load pengguna
    $items = Laporan::with('pengguna')
                ->where('id_pengguna', auth()->user()->id_pengguna)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

    return view('laporan.index', compact('items'));
}



    /**
     * Proses generate laporan.
     */
    public function generate(Request $request)
{
    $request->validate([
        'filter_type'  => 'required|in:minggu,bulan,tahun',
        'filter_date'  => 'nullable|date|required_if:filter_type,minggu',
        'filter_month' => 'nullable|date_format:Y-m|required_if:filter_type,bulan',
        'filter_year'  => 'nullable|integer|min:2000|required_if:filter_type,tahun',
    ]);

    $userId = auth()->id();
    $type   = $request->input('filter_type');

    // Tentukan start/end & label
    if ($type === 'minggu') {
        $sel   = Carbon::parse($request->input('filter_date'));
        $start = $sel->startOfWeek()->startOfDay();
        $end   = $sel->endOfWeek()->endOfDay();
        $label = 'Minggu ' . $start->format('d M Y') . ' – ' . $end->format('d M Y');
    }
    elseif ($type === 'bulan') {
        [$year, $month] = explode('-', $request->input('filter_month'));
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        $label = $start->format('F Y');
    }
    else {
        $year  = (int) $request->input('filter_year');
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();
        $label = 'Tahun ' . $year;
    }

    // Hitung ringkasan
    $totalPemasukan = Pemasukan::where('id_pengguna', $userId)
        ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
        ->sum('jumlah');

    $totalPengeluaran = Pengeluaran::where('id_pengguna', $userId)
        ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
        ->sum('jumlah');

    $saldoAkhir = $totalPemasukan - $totalPengeluaran;

    // Cek apakah sudah ada laporan untuk periode ini
    $laporan = Laporan::where('id_pengguna', $userId)
                      ->where('periode', $label)
                      ->first();

    if ($laporan) {
        // Update existing
        $laporan->update([
            'total_pemasukan'   => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo_akhir'       => $saldoAkhir,
        ]);
        $message = 'Laporan untuk ' . $label . ' berhasil diperbarui.';
    } else {
        // Buat baru
        Laporan::create([
            'id_pengguna'       => $userId,
            'total_pemasukan'   => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo_akhir'       => $saldoAkhir,
            'periode'           => $label,
        ]);
        $message = 'Laporan untuk ' . $label . ' berhasil dibuat.';
    }

    return redirect()->route('laporan.index')
                     ->with('success', $message);
}


    /**
     * Cetak PDF laporan beserta detail kategori.
     */
    public function print(Laporan $laporan)
    {
        // Parse periode → start/end
        $label = $laporan->periode;

    // 1) Mingguan
    if (Str::startsWith($label, 'Minggu ')) {
        [$from, $to] = explode(' – ', Str::after($label, 'Minggu '));
        $start = Carbon::createFromFormat('d M Y', trim($from))->startOfDay();
        $end   = Carbon::createFromFormat('d M Y', trim($to))->endOfDay();
    }
    // 2) Tahunan (sekarang diprioritaskan sebelum "bulan")
    elseif (Str::startsWith($label, 'Tahun ')) {
        $year  = (int) Str::after($label, 'Tahun ');
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();
    }
    // 3) Bulanan
    elseif (preg_match('/^[A-Z][a-z]+ \d{4}$/', $label)) {
        // ini hanya akan menang untuk "June 2025", "July 2025", dst.
        $start = Carbon::createFromFormat('F Y', $label)->startOfMonth()->startOfDay();
        $end   = Carbon::createFromFormat('F Y', $label)->endOfMonth()->endOfDay();
    }
    // 4) Fallback: periode satu hari
    else {
        $start = $laporan->created_at->startOfDay();
        $end   = $laporan->created_at->endOfDay();
    }

        // Ambil detail dengan relasi kategori
        $pemasukans = Pemasukan::with('kategori')
            ->where('id_pengguna', $laporan->id_pengguna)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')->get();

        $pengeluarans = Pengeluaran::with('kategori')
            ->where('id_pengguna', $laporan->id_pengguna)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')->get();

        // Generate PDF
        $pdf = Pdf::loadView('laporan.print', [
            'laporan'      => $laporan,
            'generated_at' => now()->format('d M Y H:i'),
            'pemasukans'   => $pemasukans,
            'pengeluarans' => $pengeluarans,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("laporan_{$laporan->id_laporan}.pdf");
    }
}
