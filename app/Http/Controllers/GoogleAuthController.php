<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $params = http_build_query([
            'client_id'     => env('GOOGLE_DRIVE_CLIENT_ID'),
            'redirect_uri'  => route('google.callback'),
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/drive.file',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$params}");
    }

    public function handleCallback(Request $request)
    {
        $code = $request->get('code');
        if (! $code) {
            return redirect()->route('dashboard')
                             ->with('error', 'Google OAuth gagal: kode tidak tersedia.');
        }

        $resp = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => env('GOOGLE_DRIVE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'redirect_uri'  => route('google.callback'),
            'grant_type'    => 'authorization_code',
        ]);

        if ($resp->failed()) {
            return redirect()->route('dashboard')
                             ->with('error', 'Gagal mendapatkan token: '.$resp->body());
        }

        $data = $resp->json();

        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $user->google_access_token     = $data['access_token'];
        $user->google_refresh_token    = $data['refresh_token'];
        $user->google_token_expires_at = Carbon::now()->addSeconds($data['expires_in']);
        $user->save();

        return redirect()->route('dashboard')
                         ->with('success', 'Akun Google Drive berhasil terhubung!');
    }

    public function disconnect()
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $user->google_access_token     = null;
        $user->google_refresh_token    = null;
        $user->google_token_expires_at = null;
        $user->save();

        return back()->with('success', 'Koneksi Google Drive berhasil dilepas.');
    }
}
