<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Cek user berdasarkan email
        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->status) {
                // Jika status user nonaktif
                return back()->withErrors(['loginError' => 'Your account is inactive.']);
            }

            Auth::loginUsingId($user->id);
            return redirect()->intended('/dashboard');
        } else {
            return back()->withErrors(['loginError' => 'Invalid email or password.']);
        }
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Sesuaikan dengan kolom `email`
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Tambahkan user ke database
        $userId = DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Login user secara otomatis
        // Auth::loginUsingId($userId);

        return redirect()->intended('/login');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/login');
    }
}
