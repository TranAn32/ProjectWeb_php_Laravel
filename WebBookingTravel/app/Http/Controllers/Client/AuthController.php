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

        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'userName';

        $user = User::query()->where($field, $data['login'])->first();

        // Kiểm tra trạng thái Active nếu có cột status
        if ($user && Schema::hasColumn('Users', 'status')) {
            $status = (string)($user->status ?? '');
            if (strcasecmp($status, 'Active') !== 0) {
                return back()->withErrors(['login' => 'Tài khoản chưa được kích hoạt'])->onlyInput('login');
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
            'userName' => 'required|string|min:3|max:50|unique:Users,userName',
            'email' => 'required|email|max:100|unique:Users,email',
            'password' => 'required|string|min:6|confirmed',
            'phoneNumber' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|in:Male,Female,Other',
        ]);

        $user = User::create([
            'userName' => $data['userName'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phoneNumber' => $data['phoneNumber'] ?? null,
            'address' => $data['address'] ?? null,
            'gender' => $data['gender'] ?? null,
            'role' => 'customer',
            'status' => 'Active',
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
