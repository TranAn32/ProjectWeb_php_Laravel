<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    // Actual table per provided schema is singular 'Tour'
    protected $table = 'Tour';
    protected $primaryKey = 'tourID';
    public $timestamps = false;

    protected $fillable = [
        'categoryID',
        'title',
        'description',
        'quantity',
        'priceAdult',
        'priceChild',
        'availability',
        'startDate',
        'endDate',
        'pickupPoint',
        'departurePoint',
        'destinationPoint',
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

    public function images()
    {
        return $this->hasMany(Image::class, 'tourID', 'tourID');
    }

    public function getImagePathAttribute()
    {
        // Prefer first image URL if available (for cover display)
        $first = $this->images()->orderBy('imageID')->first();
        return $first?->imageURL; // May be an absolute URL per your seed data
    }

    // Accessor for compat: $tour->id
    public function getIdAttribute()
    {
        return $this->tourID;
    }
}
