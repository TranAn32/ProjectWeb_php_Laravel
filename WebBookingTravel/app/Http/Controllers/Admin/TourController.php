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
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'departurePoint' => ['nullable', 'string', 'max:255'],
            'pickupPoint' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:draft,published,canceled'],
            // optional simple inputs to build prices JSON
            'priceAdult' => ['nullable', 'numeric', 'min:0'],
            'priceChild' => ['nullable', 'numeric', 'min:0'],
            // optional raw JSON inputs
            'images' => ['nullable', 'string'],
            'prices' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'hotels' => ['nullable', 'string'],
        ]);

        // Build JSON columns from provided fields if present
        if (!empty($data['priceAdult']) || !empty($data['priceChild'])) {
            $prices = [
                'adult' => isset($data['priceAdult']) ? (float)$data['priceAdult'] : null,
                'child' => isset($data['priceChild']) ? (float)$data['priceChild'] : null,
            ];
            $data['prices'] = json_encode($prices);
            unset($data['priceAdult'], $data['priceChild']);
        }
        // Validate JSON strings are valid JSON
        foreach (['images', 'prices', 'itinerary', 'hotels'] as $jsonField) {
            if (isset($data[$jsonField]) && $data[$jsonField] !== null && $data[$jsonField] !== '') {
                json_decode($data[$jsonField], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // If invalid, wrap as string array for safety
                    $data[$jsonField] = json_encode([$data[$jsonField]]);
                }
            }
        }
        return $data;
    }
}
