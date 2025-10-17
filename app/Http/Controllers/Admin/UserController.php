<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Exclude admin accounts from the listing (case-insensitive)
        $query->where(function ($q) {
            $q->whereNull('role')
                ->orWhereRaw('LOWER(role) <> ?', ['admin']);
        });

        // Optional quick search by username/email/phone
        if ($search = trim((string) $request->get('q', ''))) {
            $query->where(function ($q) use ($search) {
                // DB column is 'userName' (camelCase)
                $q->where('userName', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        $users = $query->orderByDesc('user_id')->paginate(12)->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $current = strtolower((string) $user->status);
        $new = $request->input('status');
        if ($new === null) {
            // Toggle when not provided (case-insensitive)
            $new = $current === 'active' ? 'Inactive' : 'Active';
        } else {
            $new = strtolower((string) $new);
            if ($new === 'active') {
                $new = 'Active';
            } elseif ($new === 'inactive') {
                $new = 'Inactive';
            }
        }

        if (!in_array($new, ['Active', 'Inactive'], true)) {
            return back()->with('error', 'Trạng thái không hợp lệ.');
        }

        $user->status = $new;
        $user->save();

        $msg = $new === 'Inactive' ? 'Đã khóa (ban) người dùng.' : 'Đã mở khóa người dùng.';
        return back()->with('success', $msg);
    }
}
