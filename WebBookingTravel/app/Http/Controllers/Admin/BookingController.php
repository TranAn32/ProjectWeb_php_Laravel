<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['tour', 'user'])->orderByDesc('bookingID')->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }
}
