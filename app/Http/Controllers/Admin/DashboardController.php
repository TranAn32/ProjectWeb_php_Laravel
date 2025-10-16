<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Booking;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

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

        // Handle schema variations
        $bookingTable = (new Booking)->getTable();
        $dateCol = Schema::hasColumn($bookingTable, 'bookingDate')
            ? 'bookingDate'
            : (Schema::hasColumn($bookingTable, 'booking_date')
                ? 'booking_date'
                : (Schema::hasColumn($bookingTable, 'created_at') ? 'created_at' : null));
        $totalCol = Schema::hasColumn($bookingTable, 'totalPrice') ? 'totalPrice' : (Schema::hasColumn($bookingTable, 'total_price') ? 'total_price' : null);
        $tourIdCol = Schema::hasColumn($bookingTable, 'tourID') ? 'tourID' : (Schema::hasColumn($bookingTable, 'tour_id') ? 'tour_id' : 'tourID');

        $confirmedVals = ['Confirmed', 'confirmed'];
        $pendingVals = ['Pending', 'pending'];
        $cancelledVals = ['Cancelled', 'cancelled', 'canceled'];

        // Booking statistics by status
        $bookingStats = [
            'total' => Booking::count(),
            'confirmed' => Booking::whereIn('status', $confirmedVals)->count(),
            'pending' => Booking::whereIn('status', $pendingVals)->count(),
            'cancelled' => Booking::whereIn('status', $cancelledVals)->count(),
        ];

        // Pending notification count
        $pendingCount = $bookingStats['pending'];

        // Revenue by month for the last 6 months (confirmed bookings)
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->startOfMonth();
        });

        $revenueSeries = $months->map(function ($monthStart) use ($dateCol, $totalCol, $confirmedVals) {
            $monthEnd = (clone $monthStart)->endOfMonth();
            if (!$dateCol || !$totalCol) {
                return [
                    'label' => $monthStart->format('m/Y'),
                    'value' => 0.0,
                ];
            }
            $sum = Booking::whereIn('status', $confirmedVals)
                ->whereBetween($dateCol, [$monthStart, $monthEnd])
                ->sum($totalCol);
            return [
                'label' => $monthStart->format('m/Y'),
                'value' => (float) $sum,
            ];
        })->values();

        // Cancellation rate overall (cancelled vs confirmed)
        $confirmedCount = $bookingStats['confirmed'];
        $cancelledCount = $bookingStats['cancelled'];
        $cancellationRate = ($confirmedCount + $cancelledCount) > 0
            ? round(($cancelledCount * 100) / ($confirmedCount + $cancelledCount), 2)
            : 0.0;

        // Top booked tours (by confirmed bookings count)
        $topTours = Booking::selectRaw($tourIdCol . ' as tour_id_key, COUNT(*) as cnt')
            ->whereIn('status', $confirmedVals)
            ->groupBy($tourIdCol)
            ->orderByDesc('cnt')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                $tour = Tour::find($r->tour_id_key);
                return [
                    'tourID' => $r->tour_id_key,
                    'title' => $tour->title ?? ('Tour #' . $r->tour_id_key),
                    'count' => (int) $r->cnt,
                ];
            });

        // Recent confirmed and cancelled bookings for tables
        $orderCol = $dateCol ?: (Schema::hasColumn($bookingTable, 'bookingDate')
            ? 'bookingDate'
            : (Schema::hasColumn($bookingTable, 'booking_date')
                ? 'booking_date'
                : (Schema::hasColumn($bookingTable, 'created_at')
                    ? 'created_at'
                    : (Schema::hasColumn($bookingTable, 'booking_id') ? 'booking_id' : 'bookingID'))));

        $recentConfirmed = Booking::with('tour')
            ->whereIn('status', $confirmedVals)
            ->orderByDesc($orderCol)
            ->limit(8)
            ->get();

        $recentCancelled = Booking::with('tour')
            ->whereIn('status', $cancelledVals)
            ->orderByDesc($orderCol)
            ->limit(8)
            ->get();

        // Monthly revenue raw numbers for a small table
        $monthlyRevenue = $months->map(function ($m) use ($dateCol, $totalCol, $confirmedVals) {
            $end = (clone $m)->endOfMonth();
            $val = ($dateCol && $totalCol)
                ? (float) Booking::whereIn('status', $confirmedVals)
                    ->whereBetween($dateCol, [$m, $end])
                    ->sum($totalCol)
                : 0.0;
            return [
                'label' => $m->format('m/Y'),
                'value' => $val,
            ];
        });

        // Monthly counts for confirmed vs cancelled (for grouped bar chart)
        $monthlyStatus = $months->map(function ($m) use ($dateCol, $confirmedVals, $cancelledVals) {
            $end = (clone $m)->endOfMonth();
            $confirmed = $dateCol ? Booking::whereIn('status', $confirmedVals)
                ->whereBetween($dateCol, [$m, $end])->count() : 0;
            $cancelled = $dateCol ? Booking::whereIn('status', $cancelledVals)
                ->whereBetween($dateCol, [$m, $end])->count() : 0;
            return [
                'label' => $m->format('m/Y'),
                'confirmed' => $confirmed,
                'cancelled' => $cancelled,
            ];
        });

        // Totals
        $totalTours = $tourStats['total'];
        $totalCategories = Category::count();

        return view('admin.dashboard.index', [
            'tourStats' => $tourStats,
            'bookingStats' => $bookingStats,
            'pendingCount' => $pendingCount,
            'revenueSeries' => $revenueSeries,
            'cancellationRate' => $cancellationRate,
            'topTours' => $topTours,
            'totalTours' => $totalTours,
            'totalCategories' => $totalCategories,
            'recentConfirmed' => $recentConfirmed,
            'recentCancelled' => $recentCancelled,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyStatus' => $monthlyStatus,
        ]);
    }
}
