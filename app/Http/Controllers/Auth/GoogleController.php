<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)), // Dummy password
                    'avatar' => $googleUser->getAvatar(), // Optional, if you want to save avatar
                    // Add other default fields if necessary (like age, height, etc. to avoid errors if they are required)
                    // For now assuming nullable or defaults exist for 'usia', 'tb', 'bb', etc.
                    // If they are required, we might need to redirect to a 'complete profile' page.
                ]);
            } else {
                // Update google_id if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }

            Auth::login($user);

            return redirect()->intended('homepage');

        } catch (\Exception $e) {
            return redirect('login')->with('error', 'Something went wrong with Google Login: ' . $e->getMessage());
        }
    }
}
