<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index()
    {
        $bookings = Booking::with(['tour', 'user'])
            ->orderBy('booking_id', 'desc')
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        return redirect()->back()->with('success', 'Trạng thái booking đã được cập nhật thành công!');
    }

    /**
     * Show booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['tour', 'user'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking đã được xóa thành công!');
    }
}
