<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();

        $query = Booking::with('tour')
            ->where('user_id', $user->user_id);

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('booking_id')->paginate(10);

        // Giữ query parameters khi phân trang
        $bookings->appends($request->query());

        return view('client.booking.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $tourId = (int) $request->query('tour');
        $tour = $tourId ? Tour::where('tourID', $tourId)->first() : null;
        return view('client.booking.create', compact('tour'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'tour_id' => ['required', 'integer', 'exists:tours,tourID'],
            'departure_date' => ['required', 'date'],
            'num_adults' => ['required', 'integer', 'min:1'],
            'num_children' => ['nullable', 'integer', 'min:0'],
            'special_request' => ['nullable', 'string'],
            'pickup_point' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        $tour = Tour::where('tourID', $validated['tour_id'])->firstOrFail();

        $adultPrice = (float) ($tour->priceAdult ?? 0);
        $childPrice = (float) ($tour->priceChild ?? 0);
        if (empty($adultPrice) && is_string($tour->prices)) {
            $p = json_decode($tour->prices, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($p)) {
                $adultPrice = (float) ($p['adult'] ?? 0);
                $childPrice = (float) ($p['child'] ?? 0);
            }
        }

        $numAdults = (int) $validated['num_adults'];
        $numChildren = (int) ($validated['num_children'] ?? 0);
        $total = ($numAdults * $adultPrice) + ($numChildren * $childPrice);

        Booking::create([
            'tour_id' => $tour->tourID,
            'user_id' => $user->user_id,
            'departure_date' => $validated['departure_date'],
            'num_adults' => $numAdults,
            'num_children' => $numChildren,
            'total_price' => $total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'special_request' => $validated['special_request'] ?? null,
            'pickup_point' => $validated['pickup_point'],
            'phone_number' => $validated['phone_number'],
        ]);

        return redirect()->route('client.bookings.index')->with('success', 'Đặt tour thành công');
    }

    public function cancel($id)
    {
        $user = Auth::guard('web')->user();

        $booking = Booking::where('booking_id', $id)
            ->where('user_id', $user->user_id)
            ->firstOrFail();

        // Chỉ cho phép hủy booking có status là 'pending'
        if ($booking->status !== 'pending') {
            return redirect()->route('client.bookings.index')
                ->with('error', 'Không thể hủy booking này. Chỉ có thể hủy các booking đang chờ xử lý.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return redirect()->route('client.bookings.index')
            ->with('success', 'Đã hủy đặt tour thành công.');
    }
}

