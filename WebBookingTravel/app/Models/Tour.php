<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $table = 'tours';
    protected $primaryKey = 'tourID';
    // Enable timestamps so Eloquent returns Carbon instances for created_at/updated_at
    public $timestamps = true;

    protected $fillable = [
        'categoryID',
        'title',
        'description',
        'image_path',   // single primary image path (physical or URL)
        'images',       // JSON or CSV
        'prices',       // JSON { adult, child, ... }
        'itinerary',    // JSON
        'pickupPoint',
        'departurePoint',
        'destinationPoint',
        'hotels',       // JSON
        'status',
    ];

    // Back-compat accessors used by existing blades
    public function getNameAttribute()
    {
        return $this->title;
    }
    public function getPriceAttribute()
    {
        return $this->priceAdult;
    }
    public function getDaysAttribute()
    {
        if ($this->startDate && $this->endDate) {
            return max(1, (new \DateTime($this->endDate))->diff(new \DateTime($this->startDate))->days + 1);
        }
        return null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryID', 'categoryID');
    }

    /**
     * Accessor for image_path (primary tour image)
     * Order of resolution:
     *  1. Explicit DB column 'image_path' if set
     *  2. First image derived from 'images' (JSON / CSV / array)
     * Returns raw path/URL (caller can wrap with asset() if relative).
     */
    public function getImagePathAttribute($value)
    {
        if (is_string($value) && trim($value) !== '') {
            $val = trim($value);
            if ($this->isAbsoluteUrl($val) || str_starts_with($val, '/')) return $val;
            return asset($val);
        }

        $raw = $this->attributes['images'] ?? $this->images ?? null;
        if (!$raw) return null;

        // If string: try JSON, CSV, or single
        if (is_string($raw)) {
            $trim = trim($raw);
            if ($trim === '') return null;
            $decoded = json_decode($trim, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $found = $this->extractFirstImageFromArray($decoded);
                if ($found) return $this->normalizeImageReturn($found);
            }
            if (str_contains($trim, ',')) {
                $parts = array_filter(array_map('trim', explode(',', $trim)));
                $found = $this->extractFirstImageFromArray($parts);
                if ($found) return $this->normalizeImageReturn($found);
            }
            return $this->normalizeImageReturn($trim); // single path
        }

        if (is_array($raw)) {
            $found = $this->extractFirstImageFromArray($raw);
            if ($found) return $this->normalizeImageReturn($found);
        }
        return null;
    }

    protected function normalizeImageReturn(string $path): string
    {
        $path = trim($path);
        if ($path === '') return $path;
        if ($this->isAbsoluteUrl($path) || str_starts_with($path, '/')) return $path;
        return asset($path);
    }

    protected function isAbsoluteUrl(string $val): bool
    {
        return str_starts_with($val, 'http://') || str_starts_with($val, 'https://') || str_starts_with($val, '//') || str_starts_with($val, 'data:');
    }

    protected function extractFirstImageFromArray($data)
    {
        if (!is_array($data)) return null;

        // Possible wrappers
        foreach (['images', 'photos', 'gallery', 'media', 'files'] as $k) {
            if (isset($data[$k])) {
                $res = $this->extractFirstImageFromArray($data[$k]);
                if ($res) return $res;
            }
        }

        // If associative with direct fields
        if ($this->isAssoc($data)) {
            foreach (['url', 'src', 'path', 'image', 'imageUrl', 'image_url'] as $k) {
                if (!empty($data[$k]) && is_string($data[$k])) return $data[$k];
            }
        }

        // If list
        $values = array_values($data);
        if (empty($values)) return null;
        $first = $values[0];
        if (is_string($first)) return $first;
        if (is_array($first)) {
            foreach (['url', 'src', 'path', 'image', 'imageUrl', 'image_url'] as $k) {
                if (!empty($first[$k]) && is_string($first[$k])) return $first[$k];
            }
        }
        return null;
    }

    protected function isAssoc(array $arr): bool
    {
        if ([] === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    // Accessor for compat: $tour->id
    public function getIdAttribute()
    {
        return $this->tourID;
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'startDate'  => 'date',
        'endDate'    => 'date',
        'images'     => 'array',
        'prices'     => 'array',
        'itinerary'  => 'array',
        'hotels'     => 'array',
    ];

    // Accessors for derived prices from JSON (fallback to legacy columns if present)
    public function getPriceAdultAttribute()
    {
        // If legacy column exists in attributes and prices JSON empty -> use it
        $rawCol = $this->attributes['priceAdult'] ?? null;
        $prices = $this->attributes['prices'] ?? null;
        if ($prices) {
            if (is_string($prices)) $prices = json_decode($prices, true);
            if (is_array($prices) && isset($prices['adult'])) return (float)$prices['adult'];
        }
        return $rawCol !== null ? (float)$rawCol : null;
    }
    public function getPriceChildAttribute()
    {
        $rawCol = $this->attributes['priceChild'] ?? null;
        $prices = $this->attributes['prices'] ?? null;
        if ($prices) {
            if (is_string($prices)) $prices = json_decode($prices, true);
            if (is_array($prices) && isset($prices['child'])) return (float)$prices['child'];
        }
        return $rawCol !== null ? (float)$rawCol : null;
    }
}
