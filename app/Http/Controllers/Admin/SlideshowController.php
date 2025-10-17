<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SlideshowController extends Controller
{
    private string $dir;

    public function __construct()
    {
        $this->dir = public_path('assets/images/slideShow');
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }
    }

    public function index()
    {
        $slides = $this->readSlides();
        return view('admin.slides.index', compact('slides'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => ['required'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:5120'], // 5MB
        ]);

        foreach ((array) $request->file('images', []) as $file) {
            $original = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $base = pathinfo($original, PATHINFO_FILENAME);
            $safeBase = Str::slug($base);
            if ($safeBase === '') {
                $safeBase = 'slide-' . Str::random(6);
            }
            $name = $safeBase . '.' . $ext;
            // Avoid collision
            $i = 1;
            while (file_exists($this->dir . DIRECTORY_SEPARATOR . $name)) {
                $name = $safeBase . '-' . $i++ . '.' . $ext;
            }
            $file->move($this->dir, $name);
        }

        return redirect()->route('admin.slides.index')->with('success', 'Tải ảnh lên thành công');
    }

    public function update(Request $request, string $filename)
    {
        $filename = basename($filename); // prevent path traversal
        $request->validate([
            'new_name' => ['nullable', 'string', 'max:200'],
        ]);

        $oldPath = $this->dir . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($oldPath)) {
            return redirect()->route('admin.slides.index')->withErrors(['file' => 'Tệp không tồn tại']);
        }

        // Rename file if requested
        if ($request->filled('new_name')) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $safeBase = Str::slug(pathinfo($request->input('new_name'), PATHINFO_FILENAME));
            if ($safeBase) {
                $newFilename = $safeBase . '.' . strtolower($ext);
                $i = 1;
                while ($newFilename !== $filename && file_exists($this->dir . DIRECTORY_SEPARATOR . $newFilename)) {
                    $newFilename = $safeBase . '-' . $i++ . '.' . strtolower($ext);
                }
                $newPath = $this->dir . DIRECTORY_SEPARATOR . $newFilename;
                if ($newFilename !== $filename) {
                    @rename($oldPath, $newPath);
                    $filename = $newFilename;
                }
            }
        }
        return redirect()->route('admin.slides.index')->with('success', 'Cập nhật slide thành công');
    }

    public function destroy(string $filename)
    {
        $filename = basename($filename);
        $path = $this->dir . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($path)) {
            @unlink($path);
        }
        return redirect()->route('admin.slides.index')->with('success', 'Đã xóa ảnh');
    }

    private function readSlides(): array
    {
        $entries = @scandir($this->dir) ?: [];
        $files = [];
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') continue;
            $path = $this->dir . DIRECTORY_SEPARATOR . $e;
            if (is_file($path) && preg_match('/\.(jpe?g|png|gif|webp|avif)$/i', $e)) {
                $files[] = $e;
            }
        }
        natsort($files);
        $slides = [];
        foreach ($files as $base) {
            $f = $this->dir . DIRECTORY_SEPARATOR . $base;
            $mtime = @filemtime($f) ?: time();
            $src = asset('assets/images/slideShow/' . $base) . '?v=' . $mtime;
            $slides[] = [
                'file' => $base,
                'src' => $src,
                'title' => pathinfo($base, PATHINFO_FILENAME),
                'url' => '#',
                'size' => File::exists($f) ? File::size($f) : 0,
                'mtime' => $mtime,
            ];
        }
        return $slides;
    }
}
