<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'propertyCount' => Property::count(),
            'featuredProperties' => Property::query()
                ->with('units')
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }
}
