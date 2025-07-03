<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class LaporanBackupController extends Controller
{
    protected function getAccessToken(): ?string
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        if (! $user->google_refresh_token) {
            return null;
        }

        if (
            ! $user->google_access_token ||
            $user->google_token_expires_at->lt(Carbon::now()->addMinute())
        ) {
            $resp = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id'     => env('GOOGLE_DRIVE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
                'refresh_token' => $user->google_refresh_token,
                'grant_type'    => 'refresh_token',
            ]);

            if ($resp->failed()) {
                return null;
            }

            $data = $resp->json();

            // assignment + save()
            $user->google_access_token     = $data['access_token'];
            $user->google_token_expires_at = Carbon::now()->addSeconds($data['expires_in']);
            $user->save();
        }

        return $user->google_access_token;
    }

    public function backup(Request $request)
    {
        $user    = Auth::user();
        $records = Laporan::where('id_pengguna', $user->id_pengguna)->get();

        if ($records->isEmpty()) {
            return back()->with('error', 'Tidak ada data laporan untuk dibackup.');
        }

        $token = $this->getAccessToken();
        if (! $token) {
            return back()->with('error', 'Silakan hubungkan akun Google Drive Anda terlebih dahulu.');
        }

        $filename    = 'backup_laporan_'.$user->id_pengguna.'_'.now()->format('Ymd_His').'.json';
        $jsonContent = $records->toJson(JSON_PRETTY_PRINT);

        $resp = Http::withHeaders([
                    'Authorization' => "Bearer {$token}",
                ])
                ->attach('metadata', json_encode([
                    'name'     => $filename,
                    'mimeType' => 'application/json',
                ]), 'metadata.json')
                ->attach('file', $jsonContent, $filename, [
                    'Content-Type' => 'application/json',
                ])
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        if ($resp->failed()) {
            return back()->with('error', 'Gagal mengunggah ke Google Drive: '.$resp->body());
        }

        return back()->with('success', "Backup berhasil: {$filename}");
    }

    public function restore(Request $request)
    {
        $user  = Auth::user();
        $token = $this->getAccessToken();

        if (! $token) {
            return back()->with('error', 'Silakan hubungkan akun Google Drive Anda terlebih dahulu.');
        }

        $files = Http::withToken($token)
            ->get('https://www.googleapis.com/drive/v3/files', [
                'q'        => "name contains 'backup_laporan_{$user->id_pengguna}_'",
                'orderBy'  => 'createdTime desc',
                'pageSize' => 1,
                'fields'   => 'files(id,name)',
            ])
            ->json('files', []);

        if (empty($files)) {
            return back()->with('error', 'File backup tidak ditemukan di Google Drive Anda.');
        }

        $fileId   = $files[0]['id'];
        $fileName = $files[0]['name'];

        $content = Http::withToken($token)
                        ->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media")
                        ->body();

        $data = json_decode($content, true);
        if (! is_array($data)) {
            return back()->with('error', 'Format file backup tidak valid.');
        }

        $restored = 0;
        foreach ($data as $item) {
            Laporan::updateOrCreate(
                ['id_laporan' => $item['id_laporan']],
                [
                    'id_pengguna'       => $user->id_pengguna,
                    'total_pemasukan'   => $item['total_pemasukan']   ?? 0,
                    'total_pengeluaran' => $item['total_pengeluaran'] ?? 0,
                    'saldo_akhir'       => $item['saldo_akhir']       ?? 0,
                    'periode'           => $item['periode']           ?? now()->format('Y-m'),
                ]
            );
            $restored++;
        }

        return back()->with('success', "Restore berhasil: {$restored} record dari {$fileName}.");
    }
}
