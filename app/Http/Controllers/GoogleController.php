<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Mengarahkan user ke halaman login Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Menerima callback dari Google
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Cari user di database berdasarkan email dari Google
            $user = User::where('email', $googleUser->getEmail())->first();

            // 2. Jika user ditemukan
            if ($user) {
                // Update google_id dan avatar jika masih kosong (untuk login pertama kali)
                $user->update([
                    'google_id' => $user->google_id ?? $googleUser->getId(),
                    'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                ]);

                // Login-kan user tersebut
                Auth::login($user);
                return redirect()->intended('dashboard');
            }

            // 3. Jika user tidak ditemukan
            return redirect('/login')->with('error', 'Akun Anda belum terdaftar. Silakan hubungi Administrator.');

        } catch (\Exception $e) {
            Log::error('An error occurred during Google callback.', ['error' => $e->getMessage()]);
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }
}
