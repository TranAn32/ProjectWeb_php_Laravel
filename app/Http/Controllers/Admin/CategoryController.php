<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderByDesc('categoryID')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $category = new Category();
        return view('admin.categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoryName' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'in:domestic,international'],
            'status' => ['nullable', 'in:Active,Inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'slug' => ['nullable', 'string', 'max:150'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image && $image->isValid()) {
                $categoryPath = public_path('assets/images/category');
                if (!file_exists($categoryPath)) {
                    mkdir($categoryPath, 0755, true);
                }
                $extension = $image->getClientOriginalExtension();
                $filename = uniqid() . '.' . $extension;
                $image->move($categoryPath, $filename);
                $imageUrl = 'assets/images/category/' . $filename;
            }
        }

        $data = [
            'categoryName' => $validated['categoryName'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? 'domestic',
            'status' => $validated['status'] ?? 'Active',
            'sort_order' => $validated['sort_order'] ?? 0,
            'slug' => $validated['slug'] ?? null,
            'imageURL' => $imageUrl,
        ];

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'categoryName' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'in:domestic,international'],
            'status' => ['nullable', 'in:Active,Inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'slug' => ['nullable', 'string', 'max:150'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ]);

        $data = [
            'categoryName' => $validated['categoryName'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? $category->type,
            'status' => $validated['status'] ?? $category->status,
            'sort_order' => $validated['sort_order'] ?? $category->sort_order,
            'slug' => $validated['slug'] ?? $category->slug,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image && $image->isValid()) {
                // Remove old image if exists
                if ($category->imageURL && file_exists(public_path($category->imageURL))) {
                    @unlink(public_path($category->imageURL));
                }

                $categoryPath = public_path('assets/images/category');
                if (!file_exists($categoryPath)) {
                    mkdir($categoryPath, 0755, true);
                }
                $extension = $image->getClientOriginalExtension();
                $filename = uniqid() . '.' . $extension;
                $image->move($categoryPath, $filename);
                $data['imageURL'] = 'assets/images/category/' . $filename;
            }
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Prevent deletion if has tours (optional safety)
        if ($category->tours()->exists()) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục đang có tour.');
        }

        if ($category->imageURL && file_exists(public_path($category->imageURL))) {
            @unlink(public_path($category->imageURL));
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục');
    }
}



