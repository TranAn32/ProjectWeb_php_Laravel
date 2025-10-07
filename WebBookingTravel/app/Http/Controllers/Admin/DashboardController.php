<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        // Tour statistics by status
        $tourStats = [
            'total' => Tour::count(),
            'published' => Tour::where('status', 'published')->count(),
            'draft' => Tour::where('status', 'draft')->count(),
            'cancelled' => Tour::where('status', 'cancelled')->count(),
        ];

        // Booking statistics by status
        $bookingStats = [
            'total' => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.dashboard.index', compact('tourStats', 'bookingStats'));
    }
}
