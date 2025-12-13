<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        try {
            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pendaftaran berhasil! Silakan masuk.',
                    'redirect' => route('login')
                ], 201);
            }

            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk.');
        } catch (\Exception $e) {
            // Handle any errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran gagal. Silakan coba lagi.',
                    'errors' => ['general' => 'Terjadi kesalahan saat pendaftaran.']
                ], 500);
            }

            return back()->withErrors(['general' => 'Pendaftaran gagal: ' . $e->getMessage()])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
