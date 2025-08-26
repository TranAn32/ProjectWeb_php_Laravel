<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Category;

class TourController extends Controller
{
    public function index()
    {
        $query = Tour::with('images', 'category');
        $activeDeparture = request('departure');
        if ($activeDeparture) {
            $query->where('departurePoint', $activeDeparture);
        }
        $tours = $query->orderByDesc('tourID')->paginate(12)->appends(request()->only('departure'));
        return view('client.tours', [
            'tours' => $tours,
            'activeDeparture' => $activeDeparture,
        ]);
    }

    public function show($id)
    {
        $tour = Tour::with('images', 'category')->where('tourID', $id)->firstOrFail();
        return view('client.tour_show', compact('tour'));
    }

    public function category($categoryId)
    {
        $category = Category::where('categoryID', $categoryId)->firstOrFail();
        $tours = Tour::with('images', 'category')
            ->where('categoryID', $category->categoryID)
            ->orderByDesc('tourID')
            ->paginate(12);
        return view('client.tours', [
            'tours' => $tours,
            'activeCategory' => $category,
        ]);
    }

    /**
     * Filter by type: domestic | international
     * Heuristic: destinationPoint OR title contains 'Việt Nam'/'Vietnam' -> domestic, else international.
     */
    public function type(string $type)
    {
        $validatedType = in_array($type, ['domestic', 'international']) ? $type : 'domestic';
        $tours = Tour::with('images', 'category')
            ->where('tourType', $validatedType)
            ->orderByDesc('tourID')
            ->paginate(12)
            ->appends(['type' => $validatedType]);
        return view('client.tours', [
            'tours' => $tours,
            'activeType' => $validatedType,
        ]);
    }
}
