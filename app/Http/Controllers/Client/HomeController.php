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
        // Lấy danh mục và số lượng tour trong mỗi danh mục (hiển thị cả danh mục chưa có tour)
        $categories = Category::select('categoryID', 'categoryName', 'imageURL')
            ->withCount('tours')
            ->orderBy('categoryName')
            ->take(8)
            ->get();

        // Lấy 8 tour để hiển thị ở mục "Điểm đến phổ biến"
        $popularTours = Tour::select('tourID', 'departurePoint', 'images', 'title')
            ->where('status', 'published')
            ->orderByDesc('tourID')
            ->take(8)
            ->get();

        // Lấy danh sách điểm đến từ các tour đã published
        $departures = Tour::where('status', 'published')
            ->whereNotNull('departurePoint')
            ->where('departurePoint', '!=', '')
            ->distinct()
            ->orderBy('departurePoint')
            ->pluck('departurePoint')
            ->toArray();

        // Hotels removed: no featured hotels
        $featuredHotels = [];

        return view('client.home.home', compact('slides', 'heroImage', 'categories', 'popularTours', 'featuredHotels', 'departures'));
    }
}
