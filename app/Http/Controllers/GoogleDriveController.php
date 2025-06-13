<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Laporan;

class GoogleDriveController extends Controller
{
    protected function getAccessToken()
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            abort(500, 'Gagal mendapatkan akses token Google.');
        }

        return $response->json()['access_token'];
    }

    public function backup()
    {
        $laporan = Laporan::with('pengguna')->get();
        $filename = 'backup_laporan_' . now()->format('Ymd_His') . '.json';
        $fileContent = $laporan->toJson(JSON_PRETTY_PRINT);

        // Simpan sementara
        Storage::put($filename, $fileContent);

        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->attach('file', Storage::get($filename), $filename)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart', [
                'name' => $filename,
            ]);

        Storage::delete($filename); // hapus file lokal

        return back()->with('success', 'Backup berhasil diupload ke Google Drive.');
    }

    public function restore()
    {
        $accessToken = $this->getAccessToken();

        // Cari file backup terbaru
        $files = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/drive/v3/files', [
                'q' => "name contains 'backup_laporan_'",
                'orderBy' => 'createdTime desc',
                'pageSize' => 1,
                'fields' => 'files(id, name)',
            ]);

        if (empty($files['files'])) {
            return back()->with('error', 'Tidak ada file backup ditemukan.');
        }

        $fileId = $files['files'][0]['id'];

        // Ambil konten file
        $content = Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media")
            ->body();

        $data = json_decode($content, true);

        foreach ($data as $item) {
            Laporan::updateOrCreate(
                ['id_laporan' => $item['id_laporan']],
                $item
            );
        }

        return back()->with('success', 'Data berhasil direstore dari Google Drive.');
    }
}
