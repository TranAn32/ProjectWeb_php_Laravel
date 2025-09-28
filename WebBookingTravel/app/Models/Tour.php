<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $table = 'Tour';
    protected $primaryKey = 'tourID';
    public $timestamps = false;

    protected $fillable = [
        'categoryID',
        'title',
        'description',
        'images',      // JSON
        'prices',      // JSON { adult, child, ... }
        'itinerary',   // JSON
        'pickupPoint',
        'departurePoint',
        'hotels',      // JSON
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

    // Images are stored as JSON now. Return first image url if available.
    public function getImagePathAttribute()
    {
        $raw = $this->images;

        // 1) String: try JSON, CSV, or single path
        if (is_string($raw)) {
            $rawTrim = trim($raw);
            if ($rawTrim === '') return null;
            $decoded = json_decode($rawTrim, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $url = $this->extractFirstImageFromArray($decoded);
                if ($url) return $url;
            }
            if (strpos($rawTrim, ',') !== false) {
                $parts = array_filter(array_map('trim', explode(',', $rawTrim)));
                $url = $this->extractFirstImageFromArray(array_values($parts));
                if ($url) return $url;
            }
            return $rawTrim; // single path string
        }

        // 2) Array or object-like decoded already
        if (is_array($raw)) {
            $url = $this->extractFirstImageFromArray($raw);
            if ($url) return $url;
        }

        return null;
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

    // Accessors for derived prices from JSON
    public function getPriceAdultAttribute()
    {
        $prices = $this->prices;
        if (is_string($prices)) $prices = json_decode($prices, true);
        return is_array($prices) && isset($prices['adult']) ? (float) $prices['adult'] : null;
    }
    public function getPriceChildAttribute()
    {
        $prices = $this->prices;
        if (is_string($prices)) $prices = json_decode($prices, true);
        return is_array($prices) && isset($prices['child']) ? (float) $prices['child'] : null;
    }
}
