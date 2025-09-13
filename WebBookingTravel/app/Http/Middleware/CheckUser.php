<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CheckUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();
        $statusOk = true;
        if (Schema::hasColumn('Users', 'status')) {
            $statusOk = $user && $user->status === 'Active';
        }
        if (!$user || ($user->role ?? null) !== 'customer' || !$statusOk) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect()->guest(route('login'));
        }
        return $next($request);
    }
}
