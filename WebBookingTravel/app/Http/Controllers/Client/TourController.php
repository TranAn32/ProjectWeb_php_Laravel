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
        $activeType = request('type'); // 'domestic' | 'international' | null
        $activeCategoryId = request('category');
        $keyword = trim((string) request('q')) ?: null;
        if ($keyword) {
            $kw = mb_strtolower($keyword, 'UTF-8');
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%$kw%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%$kw%"]);
            });
        }
        if ($activeDeparture) {
            $query->where('departurePoint', $activeDeparture);
        }
        if ($activeCategoryId) {
            $query->where('categoryID', $activeCategoryId);
        }
        if ($activeType && in_array($activeType, ['domestic', 'international'])) {
            $table = (new Tour())->getTable();
            $typeColumn = null;
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'type')) {
                $typeColumn = 'type';
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'tourType')) {
                $typeColumn = 'tourType';
            }
            if ($typeColumn) {
                $query->whereRaw("LOWER(TRIM(`$typeColumn`)) = ?", [strtolower($activeType)]);
            }
        }
        $tours = $query->orderByDesc('tourID')->paginate(12)->appends(request()->only('departure', 'type', 'q', 'category'));
        $departures = Tour::query()->select('departurePoint')->whereNotNull('departurePoint')->distinct()->orderBy('departurePoint')->pluck('departurePoint');
        $filterCategories = Category::query()->select('categoryID', 'categoryName')->orderBy('categoryName')->get();
        $data = [
            'tours' => $tours,
            'activeDeparture' => $activeDeparture,
            'activeType' => $activeType,
            'activeCategory' => $activeCategoryId ? Category::where('categoryID', $activeCategoryId)->first() : null,
            'keyword' => $keyword,
            'departures' => $departures,
            'filterCategories' => $filterCategories,
        ];
        if (request()->ajax()) {
            return view('client.tours_list_fragment', $data);
        }
        return view('client.tours', $data);
    }

    public function show($id)
    {
        $tour = Tour::with('category')->where('tourID', $id)->firstOrFail();
        return view('client.tour_show', compact('tour'));
    }

    public function category($categoryId)
    {
        $category = Category::where('categoryID', $categoryId)->firstOrFail();
        $query = Tour::with('category')
            ->where('categoryID', $category->categoryID);
        $activeType = request('type');
        $activeDeparture = request('departure');
        $keyword = trim((string) request('q')) ?: null;
        if ($keyword) {
            $kw = mb_strtolower($keyword, 'UTF-8');
            $query->where(function ($q) use ($kw) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%$kw%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%$kw%"]);
            });
        }
        if ($activeDeparture) {
            $query->where('departurePoint', $activeDeparture);
        }
        if ($activeType && in_array($activeType, ['domestic', 'international'])) {
            $table = (new Tour())->getTable();
            $typeColumn = null;
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'type')) {
                $typeColumn = 'type';
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'tourType')) {
                $typeColumn = 'tourType';
            }
            if ($typeColumn) {
                $query->whereRaw("LOWER(TRIM(`$typeColumn`)) = ?", [strtolower($activeType)]);
            }
        }
        $tours = $query->orderByDesc('tourID')
            ->paginate(12)
            ->appends(request()->only('type', 'q', 'departure'));
        $departures = Tour::query()->select('departurePoint')->whereNotNull('departurePoint')->distinct()->orderBy('departurePoint')->pluck('departurePoint');
        $filterCategories = Category::query()->select('categoryID', 'categoryName')->orderBy('categoryName')->get();
        $data = [
            'tours' => $tours,
            'activeCategory' => $category,
            'activeType' => $activeType,
            'keyword' => $keyword,
            'departures' => $departures,
            'filterCategories' => $filterCategories,
        ];
        if (request()->ajax()) {
            return view('client.tours_list_fragment', $data);
        }
        return view('client.tours', $data);
    }

    // Action filter by type removed (schema không còn cột tourType)
}
