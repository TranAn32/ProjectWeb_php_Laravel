<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
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
        return view('client.home', compact('slides', 'heroImage'));
    }
}
