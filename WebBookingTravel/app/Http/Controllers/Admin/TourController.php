<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $categories = Category::orderBy('categoryName')->get();
        return view('admin.tours.form', compact('tour', 'categories'));
    }

    public function store(Request $request)
    {
        // Debug (safe logging – context must be array)
        Log::info('Store request received', ['keys' => array_keys($request->all())]);
        $hasImages = $request->hasFile('images');
        $hasGallery = $request->hasFile('gallery');
        Log::info('Upload field presence', [
            'images_field' => $hasImages,
            'gallery_field' => $hasGallery,
        ]);

        // Validate the request data including images (accept both images[] or gallery[] from form)
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'categoryID' => ['nullable', 'integer', 'exists:categories,categoryID'],
            'pickupPoint' => ['nullable', 'string', 'max:255'],
            'departurePoint' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:draft,published,canceled'],
            'images' => ['nullable', 'array'],
            'images.*' => [
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:2048' // 2MB in kilobytes
            ],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => [
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:2048'
            ],
            // Additional fields for prices, itinerary (hotels removed)
            'priceAdult' => ['nullable', 'numeric', 'min:0'],
            'priceChild' => ['nullable', 'numeric', 'min:0'],
            'prices' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'itinerary_text' => ['nullable', 'string'],
        ]);

        // Consolidate files (prefer images[], fallback gallery[])
        $files = $request->file('images', $request->file('gallery', []));

        // Handle image uploads
        $imageData = [];
        if (!empty($files)) {
            Log::info('Processing images upload...', ['count' => count($files)]);

            // Ensure the tours directory exists
            $toursPath = public_path('assets/images/tours');
            if (!file_exists($toursPath)) {
                mkdir($toursPath, 0755, true);
                Log::info('Created tours directory: ' . $toursPath);
            }

            foreach ($files as $index => $image) {
                try {
                    // Validate the image
                    if (!$image->isValid()) {
                        Log::error('Invalid image at index: ' . $index);
                        continue;
                    }

                    // Generate unique filename
                    $extension = $image->getClientOriginalExtension();
                    $filename = uniqid() . '.' . $extension;

                    Log::info('Uploading image: ' . $filename);

                    // Move the file to the tours directory
                    $moved = $image->move($toursPath, $filename);

                    if ($moved) {
                        Log::info('Image uploaded successfully: ' . $filename);

                        // Store image data in the format expected by the database
                        $imageData[] = [
                            'url' => 'assets/images/tours/' . $filename,
                            'description' => $validatedData['title'] . ' - Ảnh ' . ($index + 1)
                        ];
                    } else {
                        Log::error('Failed to move image: ' . $filename);
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading image at index ' . $index . ': ' . $e->getMessage());
                }
            }
        }

        // Hotels removed: ignore any hotels data

        Log::info('Final image data', ['images' => $imageData]);

        // Prepare data for database insertion
        $tourData = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
            'categoryID' => $validatedData['categoryID'] ?? null,
            'pickupPoint' => $validatedData['pickupPoint'] ?? null,
            'departurePoint' => $validatedData['departurePoint'] ?? null,
            'status' => $validatedData['status'] ?? 'draft',
            'images' => !empty($imageData) ? json_encode($imageData) : null,
        ];

        // Handle prices JSON
        if (!empty($validatedData['priceAdult']) || !empty($validatedData['priceChild'])) {
            $prices = [
                'adult' => isset($validatedData['priceAdult']) ? (float)$validatedData['priceAdult'] : null,
                'child' => isset($validatedData['priceChild']) ? (float)$validatedData['priceChild'] : null,
            ];
            $tourData['prices'] = json_encode($prices);
        } elseif (!empty($validatedData['prices'])) {
            // Validate and store raw JSON prices
            $decodedPrices = json_decode($validatedData['prices'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $tourData['prices'] = $validatedData['prices'];
            }
        }

        // Handle other JSON fields
        foreach (['itinerary'] as $jsonField) {
            if (isset($validatedData[$jsonField]) && $validatedData[$jsonField] !== null && $validatedData[$jsonField] !== '') {
                $decoded = json_decode($validatedData[$jsonField], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $tourData[$jsonField] = $validatedData[$jsonField];
                } else {
                    // If invalid JSON, wrap as string array for safety
                    $tourData[$jsonField] = json_encode([$validatedData[$jsonField]]);
                }
            }
        }


        // Create the tour
        $tour = Tour::create($tourData);

        // Redirect to tours index with success message
        return redirect()->route('admin.tours.index')->with('success', 'Tour đã được tạo thành công!');
    }

    public function edit($id)
    {
        $tour = Tour::findOrFail($id);
        $categories = Category::orderBy('categoryName')->get();
        return view('admin.tours.form', compact('tour', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $tour = Tour::findOrFail($id);

        // Validate base fields (without file arrays)
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'departurePoint' => ['nullable', 'string', 'max:255'],
            'pickupPoint' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:draft,published,canceled'],
            'priceAdult' => ['nullable', 'numeric', 'min:0'],
            'priceChild' => ['nullable', 'numeric', 'min:0'],
            'prices' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
        ]);

        // Start from existing data
        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? $tour->description,
            'pickupPoint' => $validated['pickupPoint'] ?? $tour->pickupPoint,
            'departurePoint' => $validated['departurePoint'] ?? $tour->departurePoint,
            'status' => $validated['status'] ?? $tour->status,
        ];

        // Prices JSON
        if (!empty($validated['priceAdult']) || !empty($validated['priceChild'])) {
            $updateData['prices'] = json_encode([
                'adult' => isset($validated['priceAdult']) ? (float)$validated['priceAdult'] : null,
                'child' => isset($validated['priceChild']) ? (float)$validated['priceChild'] : null,
            ]);
        } elseif (!empty($validated['prices'])) {
            json_decode($validated['prices'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $updateData['prices'] = $validated['prices'];
            }
        }

        // Itinerary JSON
        if (isset($validated['itinerary']) && $validated['itinerary'] !== '') {
            json_decode($validated['itinerary'], true);
            $updateData['itinerary'] = json_last_error() === JSON_ERROR_NONE
                ? $validated['itinerary']
                : json_encode([$validated['itinerary']]);
        }

        // Handle gallery images replacement if provided
        $newImagesFiles = $request->file('images', $request->file('gallery', []));
        if (!empty($newImagesFiles)) {
            $toursPath = public_path('assets/images/tours');
            if (!file_exists($toursPath)) {
                mkdir($toursPath, 0755, true);
            }
            $newImageData = [];
            foreach ($newImagesFiles as $index => $image) {
                if (!$image || !$image->isValid()) {
                    continue;
                }
                $extension = $image->getClientOriginalExtension();
                $filename = uniqid() . '.' . $extension;
                try {
                    $image->move($toursPath, $filename);
                    $newImageData[] = [
                        'url' => 'assets/images/tours/' . $filename,
                        'description' => ($validated['title'] ?? $tour->title) . ' - Ảnh ' . ($index + 1),
                    ];
                } catch (\Exception $e) {
                    Log::error('Update: error moving tour image ' . $filename . ': ' . $e->getMessage());
                }
            }
            if (!empty($newImageData)) {
                $updateData['images'] = json_encode($newImageData);
            }
        }

        // Hotels removed: ignore update hotels

        $tour->update($updateData);
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
