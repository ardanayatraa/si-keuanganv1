<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Support\Facades\Http;

class GoogleDriveController extends Controller
{
    /**
     * Ambil access token dari Google OAuth 2.0
     */
    protected function getAccessToken()
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan akses token Google.',
                'error' => $response->json() ?? $response->body(),
            ], 500);
        }

        return $response->json()['access_token'];
    }

    /**
     * Backup seluruh data laporan ke Google Drive
     */
    public function backup()
    {
        // Ambil data laporan
        $laporan = Laporan::with('pengguna')->get();
        $filename = 'backup_laporan_' . now()->format('Ymd_His') . '.json';
        $jsonContent = $laporan->toJson(JSON_PRETTY_PRINT);

        // Ambil access token
        $accessToken = $this->getAccessToken();
        if (!is_string($accessToken)) return $accessToken;

        // Kirim multipart request ke Google Drive
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])
        ->attach(
            'metadata',
            json_encode([
                'name' => $filename,
                'mimeType' => 'application/json',
                // 'parents' => ['your_folder_id'], // Optional: untuk menyimpan ke folder tertentu
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

        // Tangani error jika gagal upload
        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah file ke Google Drive.',
                'error' => $response->json() ?? $response->body(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Backup berhasil diupload ke Google Drive.',
            'filename' => $filename,
            'google_response' => $response->json(),
        ]);
    }

    /**
     * Restore data laporan dari file backup terbaru di Google Drive
     */
    public function restore()
    {
        $accessToken = $this->getAccessToken();
        if (!is_string($accessToken)) return $accessToken;

        // Ambil file backup terbaru
        $files = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/drive/v3/files', [
                'q' => "name contains 'backup_laporan_'",
                'orderBy' => 'createdTime desc',
                'pageSize' => 1,
                'fields' => 'files(id, name)',
            ]);

        if ($files->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil daftar file dari Google Drive.',
                'error' => $files->json() ?? $files->body(),
            ], 500);
        }

        if (empty($files['files'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada file backup ditemukan.',
            ], 404);
        }

        $fileId = $files['files'][0]['id'];
        $fileName = $files['files'][0]['name'];

        // Ambil konten file backup
        $response = Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media");

        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil konten file dari Google Drive.',
                'error' => $response->json() ?? $response->body(),
            ], 500);
        }

        $data = json_decode($response->body(), true);
        $restored = 0;

        foreach ($data as $item) {
            Laporan::updateOrCreate(
                ['id_laporan' => $item['id_laporan']],
                $item
            );
            $restored++;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil direstore dari Google Drive.',
            'restored_count' => $restored,
            'source_file' => $fileName,
        ]);
    }
}
