<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Hiển thị danh sách category
    public function index()
    {
        $categories = Category::with('tours')->get();
        return view('categories.index', compact('categories'));
    }

    // Form tạo mới category
    public function create()
    {
        return view('categories.create');
    }

    // Lưu category mới
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'Category đã được thêm');
    }

    // Form chỉnh sửa category
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Cập nhật category
    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required']);
        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'Category đã được cập nhật');
    }

    // Xóa category
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category đã được xóa');
    }

    // Hiển thị các tour của category
    public function show(Category $category)
    {
        $category->load('tours');
        return view('categories.show', compact('category'));
    }
}
