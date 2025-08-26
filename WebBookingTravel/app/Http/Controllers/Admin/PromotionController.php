<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::orderByDesc('promotionID')->paginate(20);
        return view('admin.promotions.index', compact('promotions'));
    }
}
