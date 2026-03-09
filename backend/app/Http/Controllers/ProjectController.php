<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\RoomType;
use App\Models\ExclusiveResortRental;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 3;
        $page = (int) ($request->query('page', 1));

        $roomTypes = RoomType::select(['id', 'name', 'image_path', 'created_at'])
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'title' => $r->name,
                    'image' => $r->image_path,
                    'type' => 'room',
                    'created_at' => $r->created_at,
                ];
            });

        $rentals = ExclusiveResortRental::select(['id', 'name', 'image_path', 'created_at'])
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'title' => $e->name,
                    'image' => $e->image_path,
                    'type' => 'exclusive',
                    'created_at' => $e->created_at,
                ];
            });

        $all = $roomTypes->concat($rentals)
            ->sortByDesc('created_at')
            ->values();

        $total = $all->count();
        $items = $all->forPage($page, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => url('/projects'),
                'query' => $request->query(),
            ]
        );

        return view('projects.index', ['projects' => $paginator]);
    }
}

