<?php

namespace App\Http\Controllers;

use App\Services\ResortManagementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerGuestRelationshipController extends Controller
{
    public function __construct(
        protected ResortManagementService $resortService
    ) {}

    public function index(): View
    {
        $guests = $this->resortService->getGuestHistory();
        return view('owner.resort-management.guest-management.index', compact('guests'));
    }

    public function loyalty(): View
    {
        $guests = $this->resortService->getLoyaltyProgramGuests();
        $rewards = \App\Models\LoyaltyReward::where('is_active', true)->get();
        return view('owner.resort-management.guest-management.loyalty', compact('guests', 'rewards'));
    }
}
