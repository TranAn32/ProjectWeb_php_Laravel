<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Central place for Tour related read logic / aggregation so controllers stay thin.
 */
class TourService
{
    public function paginateWithRelations(int $perPage = 10): LengthAwarePaginator
    {
        // Images now in JSON; only eager load category
        return Tour::with(['category'])->orderByDesc('tourID')->paginate($perPage);
    }

    public function findDetailed(int $id): ?Tour
    {
        return Tour::with(['category'])->where('tourID', $id)->first();
    }

    /**
     * Returns collection of [destinationPoint, count]
     */
    public function destinations(int $limit = 200): Collection
    {
        $query = Tour::query();
        $table = (new Tour)->getTable();
        $hasCol = Schema::hasColumn($table, 'destinationPoint');

        if ($hasCol) {
            $items = $query
                ->selectRaw('destinationPoint, COUNT(*) as aggregateCount')
                ->whereNotNull('destinationPoint')
                ->groupBy('destinationPoint')
                ->orderByDesc('aggregateCount')
                ->limit($limit)
                ->get()
                ->map(fn($r) => [
                    'destinationPoint' => $r->destinationPoint,
                    'count' => (int) $r->aggregateCount,
                ]);
        } else {
            $items = collect();
        }

        if ($items->isEmpty()) { // fallback (distinct titles)
            $titles = $query->select('title')->limit($limit)->pluck('title')->filter()->unique();
            $items = $titles->map(fn($t) => ['destinationPoint' => $t, 'count' => 1])->values();
        }

        return $items;
    }

    /**
     * Returns collection of [departurePoint, count]
     */
    public function departurePoints(int $limit = 200): Collection
    {
        $query = Tour::query();
        $table = (new Tour)->getTable();
        $hasCol = Schema::hasColumn($table, 'departurePoint');

        if ($hasCol) {
            $items = $query
                ->selectRaw('departurePoint, COUNT(*) as aggregateCount')
                ->whereNotNull('departurePoint')
                ->groupBy('departurePoint')
                ->orderByDesc('aggregateCount')
                ->limit($limit)
                ->get()
                ->map(fn($r) => [
                    'departurePoint' => $r->departurePoint,
                    'count' => (int) $r->aggregateCount,
                ]);
        } else {
            $items = collect();
        }

        if ($items->isEmpty()) { // fallback (distinct titles first token)
            $titles = $query->select('title')->limit($limit)->pluck('title')->filter()->unique();
            $items = $titles->map(function ($t) {
                $parts = preg_split('/\s+/', trim($t));
                return ['departurePoint' => $parts[0] ?? $t, 'count' => 1];
            })->values();
        }

        return $items;
    }

    // Backward compatibility (temporary) if older code still calls destinationPoint()
    public function destinationPoint(int $limit = 200): Collection
    {
        return $this->destinations($limit);
    }

    // Backward compatibility if older code expects departurePoint singular
    public function departurePoint(int $limit = 200): Collection
    {
        return $this->departurePoints($limit);
    }

    /**
     * Returns collection (flat) of pickup points.
     */
    public function pickupPoints(int $limit = 300): Collection
    {
        $query = Tour::query();
        $table = (new Tour)->getTable();
        $hasPickup = Schema::hasColumn($table, 'pickupPoint');

        if ($hasPickup) {
            $items = $query->whereNotNull('pickupPoint')
                ->distinct()
                ->orderBy('pickupPoint')
                ->limit($limit)
                ->pluck('pickupPoint')
                ->values();
        } else {
            $items = collect();
        }

        if ($items->isEmpty()) { // derive from first word of titles
            $raw = $query->select('title')->limit($limit)->pluck('title')->filter();
            $items = $raw->map(function ($n) {
                $parts = preg_split('/\s+/', trim($n));
                return $parts[0] ?? $n;
            })->unique()->values();
        }

        if ($items->isEmpty()) { // ultimate fallback for front-end debugging
            $debug = $query->select('title')->limit(5)->pluck('title')->filter();
            if ($debug->count()) $items = $debug->values();
        }

        return $items;
    }
}
