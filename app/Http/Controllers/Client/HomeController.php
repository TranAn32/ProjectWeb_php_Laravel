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
        // Đọc trực tiếp ảnh trong thư mục, không dùng metadata
        if (is_dir($dir)) {
            $entries = @scandir($dir) ?: [];
            $files = [];
            foreach ($entries as $e) {
                if ($e === '.' || $e === '..') continue;
                $path = $dir . DIRECTORY_SEPARATOR . $e;
                if (is_file($path) && preg_match('/\.(jpe?g|png|gif|webp|avif)$/i', $e)) {
                    $files[] = $e;
                }
            }
            natsort($files);
            foreach ($files as $base) {
                $fullPath = $dir . DIRECTORY_SEPARATOR . $base;
                $mtime = @filemtime($fullPath) ?: time();
                // Cache-busting query to ensure latest image shows immediately
                $src = asset('assets/images/slideShow/' . $base) . '?v=' . $mtime;
                $slides[] = [
                    'src' => $src,
                    'url' => '#',
                    'title' => pathinfo($base, PATHINFO_FILENAME),
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
