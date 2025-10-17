<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
        $data = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        // Use DB column names: 'email' or 'userName' (username is a virtual attribute)
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'userName';

        $user = User::query()->where($field, $data['login'])->first();

        // Kiểm tra trạng thái nếu có cột status
        if ($user && Schema::hasColumn('users', 'status')) {
            $status = (string)($user->status ?? '');
            if (strcasecmp($status, 'active') !== 0) {
                // Flash lỗi để hiện popup (toast) theo layout client
                return back()
                    ->with('error', 'Tài khoản đã bị ban do vi phạm chính sách')
                    ->with('error_duration', 5000)
                    ->onlyInput('login');
            }
        }

        if ($user && (string)$user->password === (string)$data['password']) {
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('auth_success', 'Đăng nhập thành công');
        }

        return back()->withErrors(['login' => 'Thông tin đăng nhập không đúng'])->onlyInput('login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            // DB column is userName
            'username' => 'required|string|min:3|max:50|unique:users,userName',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
            'gender' => $data['gender'] ?? null,
            'role' => 'customer',
            'status' => 'active',
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        return redirect()->intended(route('home'))->with('auth_success', 'Tạo tài khoản thành công');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
