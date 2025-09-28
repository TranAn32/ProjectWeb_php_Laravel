<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tour;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $tourId = $request->query('tour');
        $tour = null;
        if ($tourId) {
            $tour = Tour::find($tourId);
        }
        $user = Auth::user();
        return view('client.booking', compact('tour', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tourID' => 'required|integer|exists:Tour,tourID',
            'departureDate' => 'required|date',
            'numAdults' => 'required|integer|min:1',
            'numChildren' => 'nullable|integer|min:0',
            'specialRequest' => 'nullable|string|max:2000',
            'selectedHotelName' => 'nullable|string|max:255',
            'hotelSingleRooms' => 'nullable|integer|min:0',
            'hotelDoubleRooms' => 'nullable|integer|min:0',
            'hotelSinglePrice' => 'nullable|numeric|min:0',
            'hotelDoublePrice' => 'nullable|numeric|min:0',
        ]);

        $tour = Tour::findOrFail($validated['tourID']);

        $numAdults = (int) $validated['numAdults'];
        $numChildren = (int) ($validated['numChildren'] ?? 0);

        $adultPrice = $tour->priceAdult ?? 0;
        $childPrice = $tour->priceChild ?? 0;

        // Hotel: room counts (single/double)
        $hotelName = $validated['selectedHotelName'] ?? null;
        $singleRooms = (int)($validated['hotelSingleRooms'] ?? 0);
        $doubleRooms = (int)($validated['hotelDoubleRooms'] ?? 0);
        $singlePrice = (float)($validated['hotelSinglePrice'] ?? 0);
        $doublePrice = (float)($validated['hotelDoublePrice'] ?? 0);
        $tourSubtotal = $numAdults * $adultPrice + $numChildren * $childPrice;
        $hotelSubtotal = $singleRooms * $singlePrice + $doubleRooms * $doublePrice;
        $total = $tourSubtotal + $hotelSubtotal;

        // Append selected hotel/room info to special request for visibility
        $sr = $validated['specialRequest'] ?? '';
        if ($hotelName && ($singleRooms || $doubleRooms)) {
            $extra = "\n[Khách sạn] " . $hotelName . ' • Đơn: ' . $singleRooms . ' x ' . number_format($singlePrice, 0, ',', '.') . 'đ • Đôi: ' . $doubleRooms . ' x ' . number_format($doublePrice, 0, ',', '.') . 'đ • Tổng KS ' . number_format($hotelSubtotal, 0, ',', '.') . 'đ';
            $sr = trim($sr . $extra);
        }

        $booking = Booking::create([
            'tourID' => $tour->tourID,
            'userID' => Auth::id(),
            'bookingDate' => now(),
            'departureDate' => $validated['departureDate'],
            'numAdults' => $numAdults,
            'numChildren' => $numChildren,
            'totalPrice' => $total,
            'status' => 'Pending',
            'paymentStatus' => 'Unpaid',
            'specialRequest' => $sr ?: null,
        ]);

        return redirect()->route('client.tours.show', ['id' => $tour->tourID])
            ->with('success', 'Đặt tour thành công! Mã đơn: ' . $booking->bookingID);
    }
}
