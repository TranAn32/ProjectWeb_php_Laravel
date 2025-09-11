<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Authenticate against Users table; require role=admin
        $admin = User::where('email', $data['email'])->where('role', 'admin')->first();
        if ($admin) {
            $incoming = $data['password'];
            $stored   = (string) $admin->password;
            $valid = false;

            // Detect bcrypt ($2y$, $2b$) or argon ($argon2i$, etc.)
            $looksBcrypt = str_starts_with($stored, '$2y$') || str_starts_with($stored, '$2b$');
            $looksArgon  = str_starts_with($stored, '$argon2');

            if ($looksBcrypt || $looksArgon) {
                // Safe to call Hash::check
                $valid = Hash::check($incoming, $stored);
            } else {
                // Legacy: maybe plaintext or md5/sha1
                if ($incoming === $stored) {
                    $valid = true; // plaintext
                } elseif (strlen($stored) === 32 && ctype_xdigit($stored) && md5($incoming) === strtolower($stored)) {
                    $valid = true; // md5 legacy
                } elseif (strlen($stored) === 40 && ctype_xdigit($stored) && sha1($incoming) === strtolower($stored)) {
                    $valid = true; // sha1 legacy
                }

                // If legacy matched, upgrade to bcrypt
                if ($valid) {
                    $admin->password = Hash::make($incoming);
                    $admin->save();
                }
            }

            if ($valid) {
                Auth::guard('admin')->login($admin, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        }
        return back()->withErrors(['email' => 'Sai thông tin đăng nhập'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
