<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use App\Models\KitPart;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'kitsTotal' => Kit::query()->count(),

            'partsInStock' => KitPart::query()
                ->where('status', 'in_stock')
                ->count(),

            'partsSold' => KitPart::query()
                ->where('status', 'sold')
                ->count(),
        ]);
    }
}
