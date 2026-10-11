<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KitPart;
use App\Models\RepairOrder;
use App\Models\RepairOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RepairOrderController extends Controller
{
    public function index(): View
    {
        return view('admin.repair-orders.index');
    }

    public function list(Request $request): JsonResponse
    {
        $search = trim($request->string('search')->toString());

        return response()->json([
            'items' => RepairOrder::query()
                ->with('items')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('client_name', 'like', "%{$search}%")
                            ->orWhere('client_phone', 'like', "%{$search}%")
                            ->orWhere('car_info', 'like', "%{$search}%")
                            ->orWhere('master_name', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:255'],
            'car_info' => ['nullable', 'string', 'max:255'],
            'master_name' => ['nullable', 'string', 'max:255'],
        ]);

        $order = RepairOrder::create($data);

        return response()->json([
            'message' => 'Ремонтне замовлення створено',
            'item' => $order->load('items'),
        ]);
    }

    public function scan(Request $request, RepairOrder $repairOrder): JsonResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:255'],
        ]);

        return DB::transaction(function () use ($data, $repairOrder) {
            $part = KitPart::query()
                ->where('barcode', $data['barcode'])
                ->lockForUpdate()
                ->first();

            if (! $part) {
                return response()->json(['message' => 'Запчастину не знайдено'], 404);
            }

            if ($part->status === 'pending') {
                return response()->json(['message' => 'Запчастина ще не прийнята на склад'], 422);
            }

            if ($part->status === 'sold') {
                return response()->json(['message' => 'Запчастина вже продана'], 422);
            }

            RepairOrderItem::create([
                'repair_order_id' => $repairOrder->id,
                'kit_part_id' => $part->id,
                'barcode' => $part->barcode,
                'title' => $part->title,
                'purchase_price' => $part->purchase_price,
                'sale_price' => $part->sale_price,
            ]);

            $part->update([
                'status' => 'sold',
            ]);

            $repairOrder->update([
                'total_amount' => $repairOrder->items()->sum('sale_price'),
                'status' => 'payment_due',
            ]);

            return response()->json([
                'message' => 'Запчастину списано в реалізацію',
                'item' => $repairOrder->fresh()->load('items'),
            ]);
        });
    }

    public function confirmPayment(RepairOrder $repairOrder): JsonResponse
    {
        $repairOrder->update([
            'status' => 'paid',
            'paid_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Оплату підтверджено',
            'item' => $repairOrder->fresh()->load('items'),
        ]);
    }

    public function destroy(RepairOrder $repairOrder): JsonResponse
    {
        $repairOrder->delete();

        return response()->json([
            'message' => 'Замовлення видалено',
        ]);
    }
}
