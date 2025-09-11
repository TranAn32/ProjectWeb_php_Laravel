<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Category;

class TourController extends Controller
{
    public function index()
    {
        $query = Tour::with('category');
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

    // Action filter by type removed (schema không còn cột tourType)
}
