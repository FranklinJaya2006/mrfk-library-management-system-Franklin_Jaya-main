<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan form registrasi
    public function showRegisterForm()
    {
        return view('register');
    }

    // Menangani proses registrasi
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,librarian',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);        

        // Jika permintaan adalah API
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registration successful.',
                'data' => $user
            ], 201);
        }

        // Jika permintaan dari browser (form)
        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    // Menampilkan form login
    public function showLoginForm()
    {
        return view('login');
    }

    // Menangani proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            return redirect()->back()->withErrors('Invalid credentials.');
        }

        // Hapus token lama
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('authToken')->plainTextToken;

        $roleMessage = $user->role === 'admin' ? 'Selamat datang, Admin!' : 
                    ($user->role === 'librarian' ? 'Selamat datang, Librarian!' : 'Selamat datang, User!');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id_pengguna' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_message' => $roleMessage,
                ],
            ], 200);
        }

        return redirect()->route($user->role)->with('message', $roleMessage);
    }


    // Logout User
    public function logout(Request $request)
    {
        auth()->logout();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout successful.'
            ], 200);
        }

        return redirect('/login');
    }
}