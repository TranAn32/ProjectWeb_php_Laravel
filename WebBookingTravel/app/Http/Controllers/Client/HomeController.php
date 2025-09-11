<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\TourService;
use App\Models\Category;
use App\Models\Tour;

class HomeController extends Controller
{
    public function __construct(private TourService $tourService) {}
    public function index()
    {
        $heroImage = asset('assets/images/hero/hero.jpg');
        $slides = [];
        $dir = public_path('assets/images/slideShow');
        // Đọc metadata (nếu có) từ slides.json: [{"file":"slideShow (1).jpg","url":"/tour/abc","title":"Tiêu đề"}, ...]
        $metaMap = [];
        $metaFile = $dir . '/slides.json';
        if (is_file($metaFile)) {
            $raw = @file_get_contents($metaFile);
            $data = json_decode($raw, true);
            if (is_array($data)) {
                foreach ($data as $entry) {
                    if (!empty($entry['file'])) {
                        $metaMap[$entry['file']] = $entry;
                    }
                }
            }
        }
        if (is_dir($dir)) {
            $files = glob($dir . '/*.{jpg,jpeg,png,gif,webp,avif}', GLOB_BRACE) ?: [];
            usort($files, fn($a, $b) => strnatcasecmp($a, $b));
            foreach ($files as $f) {
                $base = basename($f);
                $meta = $metaMap[$base] ?? [];
                $slides[] = [
                    'src' => asset('assets/images/slideShow/' . $base),
                    'url' => $meta['url'] ?? '#',
                    'title' => $meta['title'] ?? pathinfo($base, PATHINFO_FILENAME),
                ];
            }
        }
        if (empty($slides)) {
            $slides = [['src' => $heroImage, 'url' => '#', 'title' => 'Hero']];
        }
        // Lấy danh mục và số lượng tour trong mỗi danh mục
        $categories = Category::select('categoryID', 'categoryName', 'imageURL')
            ->whereHas('tours')
            ->withCount('tours')
            ->orderBy('categoryName')
            ->take(8)
            ->get();

        // Lấy 8 tour để hiển thị ở mục "Điểm đến phổ biến"
        $popularTours = Tour::select('tourID', 'departurePoint', 'images', 'title')
            ->orderByDesc('tourID')
            ->take(8)
            ->get();

        // Lấy 4 khách sạn từ JSON hotels của các tour (không trùng tên)
        $featuredHotels = [];
        $seen = [];
        $toursWithHotels = Tour::select('tourID', 'departurePoint', 'hotels')
            ->whereNotNull('hotels')
            ->orderByDesc('tourID')
            ->get();
        foreach ($toursWithHotels as $t) {
            $hjson = $t->hotels;
            if (is_string($hjson)) $hjson = json_decode($hjson, true);
            if (!is_array($hjson)) continue;
            foreach ($hjson as $h) {
                $name = trim((string)($h['name'] ?? ($h['title'] ?? '')));
                if ($name === '') continue;
                $key = mb_strtolower($name);
                if (isset($seen[$key])) continue;
                $rating = (int) round((float)($h['rating'] ?? $h['stars'] ?? $h['rate'] ?? 0));
                $rating = max(0, min(5, $rating));
                $image = $h['image'] ?? $h['imageURL'] ?? $h['thumbnail'] ?? null;
                $featuredHotels[] = [
                    'name' => $name,
                    'rating' => $rating,
                    'departurePoint' => $t->departurePoint,
                    'image' => $image,
                ];
                $seen[$key] = true;
                if (count($featuredHotels) >= 4) break 2;
            }
        }

        return view('client.home', compact('slides', 'heroImage', 'categories', 'popularTours', 'featuredHotels'));
    }
}
