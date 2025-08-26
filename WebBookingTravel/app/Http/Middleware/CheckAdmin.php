<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check authenticated admin guard user (role optional if enforced elsewhere)
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return redirect()->route('admin.login');
        }
        // Optionally verify role column if exists
        if (isset($user->role) && !in_array($user->role, ['admin', 'superadmin'])) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'Bạn không có quyền truy cập admin.');
        }
        return $next($request);
    }
}
