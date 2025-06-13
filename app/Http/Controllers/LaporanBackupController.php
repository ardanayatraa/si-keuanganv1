<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Laporan;
use Illuminate\Support\Str;

class LaporanBackupController extends Controller
{
    /**
     * Backup data laporan milik user yang login ke Google Drive (JSON).
     */
    public function backup(Request $request)
    {
        $user = Auth::user();

        $laporans = Laporan::where('id_pengguna', $user->id)->get();

        if ($laporans->isEmpty()) {
            return back()->with('error', 'Tidak ada data laporan untuk dibackup.');
        }

        $filename = 'laporan-backup-' . $user->id . '-' . now()->format('Ymd_His') . '.json';
        $dataJson = $laporans->toJson(JSON_PRETTY_PRINT);

        Storage::disk('google')->put($filename, $dataJson);

        return back()->with('success', 'Backup berhasil diunggah ke Google Drive.');
    }

    /**
     * Restore data laporan dari file terbaru Google Drive milik user login.
     */
    public function restore(Request $request)
    {
        $user = Auth::user();
        $files = Storage::disk('google')->files();

        // Filter file backup milik user (berdasarkan prefix user_id)
        $userFiles = collect($files)
            ->filter(fn($file) => str_contains($file, 'laporan-backup-' . $user->id))
            ->sortDesc();

        $latestFile = $userFiles->first();

        if (!$latestFile) {
            return back()->with('error', 'Tidak ditemukan file backup milik Anda di Google Drive.');
        }

        $content = Storage::disk('google')->get($latestFile);
        $data = json_decode($content, true);

        if (!is_array($data)) {
            return back()->with('error', 'File backup tidak valid.');
        }

        // Hapus data lama milik user
        Laporan::where('id_pengguna', $user->id)->delete();

        // Simpan ulang data dari backup
        foreach ($data as $item) {
            Laporan::create([
                'id_laporan'        => $item['id_laporan'] ?? Str::uuid(),
                'id_pengguna'       => $user->id,
                'total_pemasukan'   => $item['total_pemasukan'] ?? 0,
                'total_pengeluaran' => $item['total_pengeluaran'] ?? 0,
                'saldo_akhir'       => $item['saldo_akhir'] ?? 0,
                'periode'           => $item['periode'] ?? now()->format('Y-m'),
            ]);
        }

        return back()->with('success', 'Restore laporan berhasil dari Google Drive.');
    }
}
