<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'categoryID';
    public $timestamps = true;

    protected $fillable = [
        'categoryName',
        'description',
        'type',
        'imageURL',
        'slug',
        'status',
        'sort_order',
    ];

    // Accessor to keep using $category->name in views
    public function getNameAttribute()
    {
        return $this->categoryName;
    }

    // Accessor for $category->id compatibility
    public function getIdAttribute()
    {
        return $this->categoryID;
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'categoryID', 'categoryID');
    }
}
