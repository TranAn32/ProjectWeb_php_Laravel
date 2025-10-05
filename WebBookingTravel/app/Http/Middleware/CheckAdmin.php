<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AdminValidationService;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     * 
     * Sử dụng AdminValidationService để đảm bảo logic đồng bộ
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        // Sử dụng service chung để validate admin
        $validation = AdminValidationService::validateAdminUser($user);

        if (!$validation['valid']) {
            // Logout nếu user không hợp lệ
            if ($user) {
                Auth::guard('admin')->logout();
            }

            return $this->redirectToLogin($request, $validation['message']);
        }

        // User hợp lệ - cho phép truy cập
        return $next($request);
    }

    /**
     * Redirect to login page với thông báo lỗi
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function redirectToLogin(Request $request, string $message = '')
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message ?: 'Unauthorized',
                'redirect' => route('admin.login')
            ], 401);
        }

        return redirect()->guest(route('admin.login'))->with('error', $message);
    }
}
