<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan ringkasan dan grafik 7 hari untuk user login.
     */
    public function index()
    {
        $userId = Auth::user()->id_pengguna;

        // 1) Total saldo dari semua rekening milik user
        $totalSaldo = Rekening::where('id_pengguna', $userId)
                              ->sum('saldo');

        // 2) Total pemasukan & pengeluaran milik user
        $totalPemasukan   = Pemasukan::where('id_pengguna', $userId)
                                      ->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('id_pengguna', $userId)
                                       ->sum('jumlah');

        // 3) Siapkan label & data per hari untuk 7 hari terakhir, milik user
        $labels          = [];
        $pemasukanData   = [];
        $pengeluaranData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[]          = $date;
            $pemasukanData[]   = Pemasukan::where('id_pengguna', $userId)
                                           ->whereDate('tanggal', $date)
                                           ->sum('jumlah');
            $pengeluaranData[] = Pengeluaran::where('id_pengguna', $userId)
                                            ->whereDate('tanggal', $date)
                                            ->sum('jumlah');
        }

        // 4) Kirim ke view
        return view('dashboard', compact(
            'totalSaldo',
            'totalPemasukan',
            'totalPengeluaran',
            'labels',
            'pemasukanData',
            'pengeluaranData'
        ));
    }
}
