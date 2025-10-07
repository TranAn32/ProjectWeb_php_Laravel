<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'user_id',
        'booking_date',
        'departure_date',
        'num_adults',
        'num_children',
        'total_price',
        'status',
        'payment_status',
        'special_request',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'tourID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}


