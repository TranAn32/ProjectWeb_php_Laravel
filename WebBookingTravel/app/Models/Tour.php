<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    // Thực tế DB đang dùng bảng 'Tour' (singular) -> giữ nguyên để tránh lỗi ngay.
    // Nếu sau này muốn chuẩn hoá plural: tạo migration rename 'Tour' => 'tours'
    // rồi đổi lại giá trị này.
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
        $val = $this->images;
        if (is_string($val)) {
            $decoded = json_decode($val, true);
        } else {
            $decoded = $val;
        }
        if (is_array($decoded) && !empty($decoded)) {
            $first = $decoded[0];
            if (is_array($first)) {
                return $first['url'] ?? null;
            }
            if (is_string($first)) {
                return $first;
            }
        }
        return null;
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
