<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
            'status' => $validatedData['status'] ?? 'draft',
            // assign arrays directly; model casts will JSON encode
            'images' => !empty($imageData) ? $imageData : null,
        ];

        // Optional columns – only set if they exist in schema
        $table = (new Tour)->getTable();
        if (Schema::hasColumn($table, 'categoryID')) {
            $tourData['categoryID'] = $validatedData['categoryID'] ?? null;
        }
        if (Schema::hasColumn($table, 'pickupPoint')) {
            $tourData['pickupPoint'] = $validatedData['pickupPoint'] ?? null;
        }
        if (Schema::hasColumn($table, 'departurePoint')) {
            $tourData['departurePoint'] = $validatedData['departurePoint'] ?? null;
        }
        if (Schema::hasColumn($table, 'destinationPoint')) {
            $tourData['destinationPoint'] = $validatedData['destinationPoint'] ?? null;
        }

        // Handle prices JSON
        if (!empty($validatedData['priceAdult']) || !empty($validatedData['priceChild'])) {
            $prices = [
                'adult' => isset($validatedData['priceAdult']) ? (float)$validatedData['priceAdult'] : null,
                'child' => isset($validatedData['priceChild']) ? (float)$validatedData['priceChild'] : null,
            ];
            $tourData['prices'] = $prices;
        } elseif (!empty($validatedData['prices'])) {
            // Validate and store raw JSON prices as array
            $decodedPrices = json_decode($validatedData['prices'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $tourData['prices'] = $decodedPrices;
            }
        }

        // Handle other JSON fields
        foreach (['itinerary'] as $jsonField) {
            if (isset($validatedData[$jsonField]) && $validatedData[$jsonField] !== null && $validatedData[$jsonField] !== '') {
                $decoded = json_decode($validatedData[$jsonField], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $tourData[$jsonField] = $decoded;
                } else {
                    // If invalid JSON, wrap as array of string for safety
                    $tourData[$jsonField] = [$validatedData[$jsonField]];
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
        try {
            $tour = Tour::findOrFail($id);

            Log::info('=== TOUR UPDATE STARTED ===');
            Log::info('Tour ID: ' . $id);
            Log::info('Request method: ' . $request->method());
            Log::info('All request data:', $request->all());
            Log::info('Files in request:', $request->allFiles());

            Log::info('Update request received', [
                'tour_id' => $id,
                'data_keys' => array_keys($request->all()),
                'has_images' => $request->hasFile('images'),
                'images_to_delete' => $request->input('images_to_delete'),
                'existing_images' => $request->input('existing_images')
            ]);

            Log::info('Starting validation...');

            // Validate base fields including new image management fields
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'categoryID' => ['nullable', 'integer', 'exists:categories,categoryID'],
                'departurePoint' => ['nullable', 'string', 'max:255'],
                'destinationPoint' => ['nullable', 'string', 'max:255'],
                'pickupPoint' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', 'in:draft,published,canceled'],
                'priceAdult' => ['nullable', 'numeric', 'min:0'],
                'priceChild' => ['nullable', 'numeric', 'min:0'],
                'prices' => ['nullable', 'string'],
                'itinerary' => ['nullable', 'string'],
                'images_to_delete' => ['nullable', 'string'],
                'existing_images' => ['nullable', 'string'],
                'images' => ['nullable', 'array'],
                'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            ]);

            Log::info('Validation passed', ['validated_keys' => array_keys($validated)]);

            // Start from existing data
            $updateData = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? $tour->description,
                'status' => $validated['status'] ?? $tour->status,
            ];

            // Optional columns – only set if they exist in schema
            $table = $tour->getTable();
            if (Schema::hasColumn($table, 'categoryID')) {
                $updateData['categoryID'] = $validated['categoryID'] ?? $tour->categoryID;
            }
            if (Schema::hasColumn($table, 'pickupPoint')) {
                $updateData['pickupPoint'] = $validated['pickupPoint'] ?? $tour->pickupPoint;
            }
            if (Schema::hasColumn($table, 'departurePoint')) {
                $updateData['departurePoint'] = $validated['departurePoint'] ?? $tour->departurePoint;
            }
            if (Schema::hasColumn($table, 'destinationPoint')) {
                $updateData['destinationPoint'] = $validated['destinationPoint'] ?? $tour->destinationPoint;
            }

            // Prices JSON
            if (!empty($validated['priceAdult']) || !empty($validated['priceChild'])) {
                $updateData['prices'] = [
                    'adult' => isset($validated['priceAdult']) ? (float)$validated['priceAdult'] : null,
                    'child' => isset($validated['priceChild']) ? (float)$validated['priceChild'] : null,
                ];
            } elseif (!empty($validated['prices'])) {
                $decoded = json_decode($validated['prices'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $updateData['prices'] = $decoded;
                }
            }

            // Itinerary JSON
            if (isset($validated['itinerary']) && $validated['itinerary'] !== '') {
                $decodedItinerary = json_decode($validated['itinerary'], true);
                $updateData['itinerary'] = json_last_error() === JSON_ERROR_NONE
                    ? $decodedItinerary
                    : [$validated['itinerary']];
            }

            // Handle image management
            $finalImages = [];

            // Get existing images from tour
            $existingImages = [];
            if ($tour->images) {
                $existingImages = is_string($tour->images) ? json_decode($tour->images, true) : $tour->images;
                if (!is_array($existingImages)) {
                    $existingImages = [];
                }
            }

            // Get images to delete
            $imagesToDelete = [];
            if (!empty($validated['images_to_delete'])) {
                $imagesToDelete = json_decode($validated['images_to_delete'], true) ?: [];
            }

            // Get remaining existing images from frontend
            $remainingExistingImages = [];
            if (!empty($validated['existing_images'])) {
                $remainingExistingImages = json_decode($validated['existing_images'], true) ?: [];
            }

            // Delete specified images from disk
            foreach ($imagesToDelete as $imageToDelete) {
                // Handle different path formats
                $imagePath = '';
                if (strpos($imageToDelete, 'assets/') === 0) {
                    // Path already starts with assets/
                    $imagePath = public_path($imageToDelete);
                } else {
                    // Assume it's just the filename or relative path
                    $imagePath = public_path('assets/images/tours/' . basename($imageToDelete));
                }

                Log::info('Attempting to delete image', ['path' => $imageToDelete, 'full_path' => $imagePath]);

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Deleted image successfully: ' . $imageToDelete);
                } else {
                    Log::warning('Image file not found for deletion: ' . $imagePath);
                }
            }

            // Add remaining existing images to final list
            foreach ($existingImages as $existingImage) {
                $imagePath = $existingImage['url'] ?? $existingImage;
                if (in_array($imagePath, $remainingExistingImages)) {
                    $finalImages[] = [
                        'url' => $imagePath,
                        'description' => $existingImage['description'] ?? ($validated['title'] . ' - Ảnh')
                    ];
                }
            }

            // Handle new image uploads
            $newImagesFiles = $request->file('images', []);
            if (!empty($newImagesFiles)) {
                $toursPath = public_path('assets/images/tours');
                if (!file_exists($toursPath)) {
                    mkdir($toursPath, 0755, true);
                }

                foreach ($newImagesFiles as $index => $image) {
                    if (!$image || !$image->isValid()) {
                        continue;
                    }

                    $extension = $image->getClientOriginalExtension();
                    $filename = uniqid() . '.' . $extension;

                    try {
                        $image->move($toursPath, $filename);
                        $finalImages[] = [
                            'url' => 'assets/images/tours/' . $filename,
                            'description' => $validated['title'] . ' - Ảnh ' . (count($finalImages) + 1),
                        ];
                        Log::info('Uploaded new image: ' . $filename);
                    } catch (\Exception $e) {
                        Log::error('Error uploading image ' . $filename . ': ' . $e->getMessage());
                    }
                }
            }

            // Update images in database only if there is actual image intent
            $hasImageIntent = (!empty($imagesToDelete) || !empty($remainingExistingImages) || $request->hasFile('images'));

            if ($hasImageIntent) {
                if (!empty($finalImages)) {
                    $updateData['images'] = $finalImages; // assign array; model casts will JSON encode
                    Log::info('Setting images in database', ['image_count' => count($finalImages)]);
                } else {
                    // When user explicitly removed all images (no remaining + no new)
                    $updateData['images'] = [];
                    Log::info('Setting empty images array');
                }
            } else {
                Log::info('No image management inputs provided; keeping existing images unchanged');
            }

            Log::info('Final update data', ['update_data_keys' => array_keys($updateData)]);

            // Hotels removed: ignore update hotels

            $tour->update($updateData);

            Log::info('Tour updated successfully', ['tour_id' => $id]);
            Log::info('=== TOUR UPDATE COMPLETED SUCCESSFULLY ===');

            return redirect()->route('admin.tours.index')->with('success', 'Đã cập nhật tour thành công');
        } catch (\Exception $e) {
            Log::error('Tour update failed', [
                'tour_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            Log::info('=== TOUR UPDATE FAILED ===');

            return back()->with('error', 'Cập nhật tour thất bại: ' . $e->getMessage());
        }
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
