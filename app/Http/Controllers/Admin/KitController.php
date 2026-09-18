<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitController extends Controller
{
    public function index(): View
    {
        return view('admin.kits.index');
    }

    public function list(): JsonResponse
    {
        $kits = Kit::query()
            ->withCount('parts')
            ->latest()
            ->get();

        return response()->json([
            'items' => $kits,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:in_stock,sold'],
        ]);

        $kit = Kit::create($data);

        return response()->json([
            'message' => 'Комплект створено',
            'item' => $kit,
        ]);
    }

    public function update(Request $request, Kit $kit): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:in_stock,sold'],
        ]);

        $kit->update($data);

        return response()->json([
            'message' => 'Комплект оновлено',
            'item' => $kit->fresh(),
        ]);
    }

    public function destroy(Kit $kit): JsonResponse
    {
        $kit->delete();

        return response()->json([
            'message' => 'Комплект видалено',
        ]);
    }
}
