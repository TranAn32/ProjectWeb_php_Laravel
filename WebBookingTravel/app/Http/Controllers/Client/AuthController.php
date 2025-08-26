<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('client.auth.login');
    }

    public function showRegister()
    {
        return view('client.auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Fetch user early
        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            $stored = (string) $user->password;
            $incoming = $credentials['password'];
            $looksModern = str_starts_with($stored, '$2y$') || str_starts_with($stored, '$2b$') || str_starts_with($stored, '$argon2');
            if (!$looksModern) {
                $match = false;
                if ($incoming === $stored) {
                    $match = true;
                } elseif (strlen($stored) === 32 && ctype_xdigit($stored) && md5($incoming) === strtolower($stored)) {
                    $match = true;
                } elseif (strlen($stored) === 40 && ctype_xdigit($stored) && sha1($incoming) === strtolower($stored)) {
                    $match = true;
                }
                if ($match) {
                    $user->password = Hash::make($incoming); // upgrade legacy
                    $user->save();
                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();
                    return redirect()->intended(route('home'))->with('auth_success', 'Đăng nhập thành công');
                }
            }
        }

        // Modern hash attempt (or user not found yet)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('auth_success', 'Đăng nhập thành công');
        }
        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng'])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:User,email',
            'password' => 'required|min:6|confirmed'
        ]);
        $user = User::create([
            'userName' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('home')->with('auth_success', 'Tạo tài khoản thành công');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
