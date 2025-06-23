<?php

namespace App\Http\Controllers\Auth;

use Throwable;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyController;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Laravel\Fortify\Http\Responses\LogoutResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;
use App\Models\Admin;

class CustomAuthenticatedSessionController extends FortifyController
{
    public function store(LoginRequest $request)
    {
        try {
            // 1) Coba login Pengguna
            $user = Pengguna::where('email', $request->email)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                auth()->guard('web')->login($user);
                session(['role' => 'pengguna']);
                return redirect()->to('/dashboard');
            }

            // 2) Coba login Admin
            $admin = Admin::where('email', $request->email)->first();

            if ($admin && Hash::check($request->password, $admin->password)) {
                auth()->guard('admin')->login($admin);
                session(['role' => 'admin']);
                return redirect()->intended('/auth/admin/dashboard');
            }

            throw new \Exception('Kredensial salah');

        } catch (Throwable $e) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Login gagal. Periksa kembali email dan password.',
                ]);
        }
    }

    public function destroy(Request $request): LogoutResponse
    {
        auth()->guard('web')->logout();
        auth()->guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('role');

        return app(LogoutResponse::class);
    }
}
