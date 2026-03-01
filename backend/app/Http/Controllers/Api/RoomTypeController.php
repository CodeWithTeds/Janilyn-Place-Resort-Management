<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function show(RoomType $roomType)
    {
        return response()->json($roomType->load('pricingTiers'));
    }
}
