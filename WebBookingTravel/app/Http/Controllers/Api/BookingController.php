<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        return response()->json(Booking::with(['tour', 'user'])->orderByDesc('bookingID')->paginate(15));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'tour_id' => 'required|integer|exists:Tour,tourID',
            'user_id' => 'nullable|integer|exists:User,userID',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $tour = Tour::find($data['tour_id']);
        $numAdults = $data['adults'];
        $numChildren = $data['children'] ?? 0;
        $adultPrice = $tour->priceAdult ?? 0;
        $childPrice = $tour->priceChild ?? ($adultPrice * 0.5); // fallback heuristic
        $total = ($numAdults * $adultPrice) + ($numChildren * $childPrice);
        $booking = Booking::create([
            'tourID' => $tour->tourID,
            'userID' => $data['user_id'] ?? null,
            'bookingDate' => now(),
            'numAdults' => $numAdults,
            'numChildren' => $numChildren,
            'totalPrice' => $total,
            'status' => 'pending',
            'specialRequest' => $data['special_request'] ?? null,
        ]);
        return response()->json($booking->load(['tour', 'user']), 201);
    }
}
