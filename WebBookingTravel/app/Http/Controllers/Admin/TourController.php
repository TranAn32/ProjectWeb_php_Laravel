<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with('category')->orderByDesc('tourID')->paginate(20);
        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        $tour = new Tour();
        return view('admin.tours.form', compact('tour'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $tour = Tour::create($data);
        return redirect()->route('admin.tours.edit', $tour->tourID)->with('success', 'Đã tạo tour');
    }

    public function edit($id)
    {
        $tour = Tour::findOrFail($id);
        return view('admin.tours.form', compact('tour'));
    }

    public function update(Request $request, $id)
    {
        $tour = Tour::findOrFail($id);
        $data = $this->validatedData($request, $tour->tourID);
        $tour->update($data);
        return back()->with('success', 'Đã cập nhật');
    }

    public function destroy($id)
    {
        $tour = Tour::findOrFail($id);
        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Đã xóa');
    }

    protected function validatedData(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priceAdult' => ['required', 'numeric', 'min:0'],
            'tourType' => ['required', 'in:domestic,international'],
            'departurePoint' => ['nullable', 'string', 'max:255'],
            'destinationPoint' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
