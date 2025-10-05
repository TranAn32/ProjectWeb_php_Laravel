<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

class AdminValidationService
{
    /**
     * Kiểm tra user có phải admin hợp lệ không
     * Logic thống nhất cho cả AuthController và CheckAdmin middleware
     * 
     * @param User|null $user
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateAdminUser($user): array
    {
        // Kiểm tra user tồn tại
        if (!$user) {
            return [
                'valid' => false,
                'message' => 'Vui lòng đăng nhập để truy cập khu vực admin.'
            ];
        }

        // Kiểm tra role admin
        if (($user->role ?? null) !== 'admin') {
            return [
                'valid' => false,
                'message' => 'Bạn không có quyền truy cập khu vực admin.'
            ];
        }

        // Kiểm tra status (nếu có column status)
        if (Schema::hasColumn('Users', 'status')) {
            if (($user->status ?? null) !== 'Active') {
                return [
                    'valid' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa.'
                ];
            }
        }

        // Tất cả kiểm tra đều pass
        return [
            'valid' => true,
            'message' => ''
        ];
    }

    /**
     * Kiểm tra credentials login cho admin
     * 
     * @param string $email
     * @param string $password
     * @return array ['user' => User|null, 'valid' => bool, 'message' => string]
     */
    public static function validateAdminCredentials(string $email, string $password): array
    {
        // Tìm user có role admin
        $admin = User::where('email', $email)->where('role', 'admin')->first();

        if (!$admin) {
            return [
                'user' => null,
                'valid' => false,
                'message' => 'Tài khoản không tồn tại hoặc không có quyền admin.'
            ];
        }

        // Kiểm tra password
        if ((string)$admin->password !== (string)$password) {
            return [
                'user' => null,
                'valid' => false,
                'message' => 'Sai thông tin đăng nhập.'
            ];
        }

        // Kiểm tra admin user hợp lệ
        $validation = self::validateAdminUser($admin);

        return [
            'user' => $admin,
            'valid' => $validation['valid'],
            'message' => $validation['message']
        ];
    }
}
