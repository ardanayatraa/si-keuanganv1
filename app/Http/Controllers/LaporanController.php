<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Tampilkan form + list laporan (history).
     */
    public function index()
    {
        $items = Laporan::with('pengguna')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('laporan.index', compact('items'));
    }

    /**
     * Tangani form generate laporan.
     * Simpan hasil ringkasan (totalP, totalPeng, saldo, dan label periode).
     */
    public function generate(Request $request)
    {

            $request->validate([
                'filter_type'  => 'required|in:minggu,bulan,tahun',

                // Hanya wajib jika filter_type == "minggu",
                // tapi biarkan null (dan skip date‐check) bila bukan "minggu"
                'filter_date'  => 'nullable|date|required_if:filter_type,minggu',

                // Hanya wajib jika filter_type == "bulan",
                // dan bila terisi, harus berupa "YYYY-MM"
                'filter_month' => 'nullable|date_format:Y-m|required_if:filter_type,bulan',

                // Hanya wajib jika filter_type == "tahun",
                // dan bila terisi, harus integer minimal 2000
                'filter_year'  => 'nullable|integer|min:2000|required_if:filter_type,tahun',
            ]);


        $userId = 1;
        $type   = $request->input('filter_type');
        $start  = null;
        $end    = null;
        $label  = '';

        if ($type === 'minggu') {
            $selected = Carbon::parse($request->input('filter_date'));
            $start = $selected->copy()->startOfWeek()->startOfDay();
            $end   = $selected->copy()->endOfWeek()->endOfDay();
            $label = 'Minggu '
                   . $start->format('d M Y')
                   . ' – '
                   . $end->format('d M Y');

        } elseif ($type === 'bulan') {
            [$year, $month] = explode('-', $request->input('filter_month'));
            $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $end   = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
            $label = $start->format('F Y'); // misal "Juni 2025"

        } else { // tahun
            $year  = (int) $request->input('filter_year');
            $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $end   = Carbon::createFromDate($year, 12, 31)->endOfDay();
            $label = 'Tahun ' . $year;
        }

        // Hitung total pemasukan selama periode itu
        $totalPemasukan = Pemasukan::where('id_pengguna', $userId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah');

        // Hitung total pengeluaran selama periode itu
        $totalPengeluaran = Pengeluaran::where('id_pengguna', $userId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah');

        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        // Simpan ke tabel laporan, dengan `periode` = label string
        Laporan::create([
            'id_pengguna'        => $userId,
            'total_pemasukan'    => $totalPemasukan,
            'total_pengeluaran'  => $totalPengeluaran,
            'saldo_akhir'        => $saldoAkhir,
            'periode'            => $label,
        ]);

        return redirect()
            ->route('laporan.index')
            ->with('success', 'Laporan berhasil dibuat untuk ' . $label);
    }
}
