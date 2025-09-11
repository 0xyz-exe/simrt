<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // User exists with Google ID, just login
                Auth::login($user);
                return redirect()->intended('/dashboard')->with('success', 'Berhasil login dengan Google!');
            }

            // Check if user exists with same email
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(),
                ]);

                Auth::login($existingUser);
                return redirect()->intended('/dashboard')->with('success', 'Akun Google berhasil dihubungkan!');
            }

            // Create new user with Google data
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'email_verified_at' => now(),
                'password' => null, // No password for Google users
            ]);

            Auth::login($newUser);
            return redirect()->intended('/dashboard')->with('success', 'Akun berhasil dibuat dengan Google!');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.');
        }
    }

    /**
     * Unlink Google account
     */
    public function unlinkGoogle(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user has password (can login without Google)
        if (!$user->password) {
            return back()->with('error', 'Tidak dapat memutus hubungan Google karena akun ini tidak memiliki password. Silakan set password terlebih dahulu.');
        }

        $user->update([
            'google_id' => null,
            'avatar' => null,
        ]);

        return back()->with('success', 'Akun Google berhasil diputus hubungannya.');
    }
}
