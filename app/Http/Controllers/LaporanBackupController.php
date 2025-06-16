<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Laporan;
use Illuminate\Support\Str;

class LaporanBackupController extends Controller
{
    /**
     * Ambil access token dari Google OAuth 2.0
     */
    protected function getAccessToken()
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id'     => env('GOOGLE_DRIVE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'grant_type'    => 'refresh_token',
        ]);

        if ($response->failed()) {
            // langsung return JSON response error agar ditangani oleh caller
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendapatkan akses token Google.',
                'error'   => $response->json() ?? $response->body(),
            ], 500);
        }

        return $response->json()['access_token'];
    }

    /**
     * Backup data laporan milik user yang login ke Google Drive (JSON).
     */
    public function backup(Request $request)
    {
        $user      = Auth::user();
        $laporans  = Laporan::where('id_pengguna', $user->id_pengguna)->get();

        if ($laporans->isEmpty()) {
            return back()->with('error', 'Tidak ada data laporan untuk dibackup.');
        }

        $filename    = 'laporan-backup-' . $user->id_pengguna . '-' . now()->format('Ymd_His') . '.json';
        $jsonContent = $laporans->toJson(JSON_PRETTY_PRINT);

        // dapatkan access token
        $accessToken = $this->getAccessToken();
        if (!is_string($accessToken)) {
            // jika getAccessToken mengembalikan response error
            return $accessToken;
        }

        // kirim multipart upload ke Drive API
        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])
            ->attach(
                'metadata',
                json_encode([
                    'name'     => $filename,
                    'mimeType' => 'application/json',
                    // 'parents' => ['FOLDER_ID'], // jika ingin simpan di folder tertentu
                ]),
                'metadata.json'
            )
            ->attach(
                'file',
                $jsonContent,
                $filename,
                ['Content-Type' => 'application/json']
            )
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        if ($response->failed()) {
            return back()->with('error', 'Gagal mengunggah file ke Google Drive.')->with('details', $response->json());
        }

        return back()->with('success', 'Backup berhasil diunggah ke Google Drive.');
    }

    /**
     * Restore data laporan dari file backup terbaru di Google Drive milik user login.
     */
    public function restore(Request $request)
    {
        $user        = Auth::user();

        $accessToken = $this->getAccessToken();
        if (!is_string($accessToken)) {
            return $accessToken;
        }

        // ambil daftar file backup user, urut berdasarkan waktu terbaru
        $files = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/drive/v3/files', [
                'q'        => "name contains 'laporan-backup-{$user->id}-'",
                'orderBy'  => 'createdTime desc',
                'pageSize' => 1,
                'fields'   => 'files(id, name)',
            ]);

        if ($files->failed()) {
            return back()->with('error', 'Gagal mengambil daftar file dari Google Drive.');
        }

        $list = $files->json('files', []);
        if (empty($list)) {
            return back()->with('error', 'Tidak ditemukan file backup milik Anda di Google Drive.');
        }

        $fileId   = $list[0]['id'];
        $fileName = $list[0]['name'];

        // ambil konten file
        $response = Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media");

        if ($response->failed()) {
            return back()->with('error', 'Gagal mengambil konten file dari Google Drive.');
        }

        $data = json_decode($response->body(), true);
        if (!is_array($data)) {
            return back()->with('error', 'File backup tidak valid.');
        }
        // hapus data lama milik user
        Laporan::where('id_pengguna', $user->id_pengguna)->delete();

        // simpan ulang dari backup
        foreach ($data as $item) {
            Laporan::create([
                'id_laporan'        => $item['id_laporan'] ?? Str::uuid(),
                'id_pengguna'       => $user->id_pengguna,
                'total_pemasukan'   => $item['total_pemasukan']   ?? 0,
                'total_pengeluaran' => $item['total_pengeluaran'] ?? 0,
                'saldo_akhir'       => $item['saldo_akhir']       ?? 0,
                'periode'           => $item['periode']           ?? now()->format('Y-m'),
            ]);
        }

        return back()->with('success', 'Restore laporan berhasil dari Google Drive.');
    }
}
