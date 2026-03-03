<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveResortRental;
use Illuminate\Http\Request;

class ExclusiveRentalController extends Controller
{
    public function show(ExclusiveResortRental $exclusiveResortRental)
    {
        return response()->json($exclusiveResortRental->load('pricingTiers'));
    }
}
