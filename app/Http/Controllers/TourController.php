<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class TourController extends Controller
{
    // Form tạo tour mới
    public function create()
    {
        $categories = Category::all();
        return view('tours.create', compact('categories'));
    }

    // Lưu tour mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'days' => 'required|integer',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Lưu vào storage/app/public/tours
            $imagePath = $request->file('image')->store('tours', 'public');
        }

        $tour = Tour::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'days' => $validated['days'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('categories.index')->with('success', 'Tour đã được thêm');
    }

    // Form sửa tour
    public function edit(Tour $tour)
    {
        $categories = Category::all();
        return view('tours.edit', compact('tour','categories'));
    }

    // Cập nhật tour
    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'days' => 'required|integer',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'days' => $validated['days'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
        ];

        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu có
            if ($tour->image_path && Storage::disk('public')->exists($tour->image_path)) {
                Storage::disk('public')->delete($tour->image_path);
            }
            $data['image_path'] = $request->file('image')->store('tours', 'public');
        }

        $tour->update($data);

        return redirect()->route('categories.show', $tour->category_id)->with('success', 'Tour đã được cập nhật');
    }

    // Xóa tour
    public function destroy(Tour $tour)
    {
        $category_id = $tour->category_id;
        // Xoá ảnh trên disk nếu có
        if ($tour->image_path && Storage::disk('public')->exists($tour->image_path)) {
            Storage::disk('public')->delete($tour->image_path);
        }
        $tour->delete();
        return redirect()->route('categories.show', $category_id)->with('success','Tour đã được xóa');
    }
    
}
