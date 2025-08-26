<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TourService;

class TourController extends Controller
{
    public function __construct(private TourService $service) {}

    public function index()
    {
        return response()->json($this->service->paginateWithRelations());
    }

    public function show(int $id)
    {
        $tour = $this->service->findDetailed($id);
        abort_if(!$tour, 404);
        return response()->json($tour);
    }

    /**
     * GET /api/v1/tours/destinations
     */
    public function destinations()
    {
        return response()->json($this->service->destinations());
    }

    /**
     * GET /api/v1/tours/departure-points
     */
    public function departurePoints()
    {
        return response()->json($this->service->departurePoints());
    }

    /**
     * GET /api/v1/tours/pickup-points
     */
    public function pickupPoints()
    {
        return response()->json($this->service->pickupPoints());
    }
}
