<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use App\Models\KitPart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Setting;
use App\Services\LabelPrinterService;

class KitPartController extends Controller
{
    public function index(): View
    {

        return view('admin.kit-parts.index', [
            'kits' => Kit::query()->orderBy('title')->get(),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $search = trim($request->string('search')->toString());

        $items = KitPart::query()
            ->with('kit:id,title')
            ->when($request->filled('kit_id'), function ($query) use ($request) {
                $query->where('kit_id', $request->integer('kit_id'));
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return response()->json([
            'items' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kit_id' => ['required', 'exists:kits,id'],
            'title' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:kit_parts,barcode'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,in_stock,sold'],
        ]);

        $printAfterSave = $request->boolean('print_after_save', true);

        $data['sequence_number'] = ((int) KitPart::query()
                ->where('kit_id', $data['kit_id'])
                ->max('sequence_number')) + 1;

        if (empty($data['barcode'])) {
            $data['barcode'] = $this->generateNextBarcode((int)$data['kit_id']);
        }

        $item = KitPart::create($data)->load('kit:id,title');

        $printResult = null;

        if ($printAfterSave) {
            $printResult = app(LabelPrinterService::class)->printPartLabel($item);
        }

        return response()->json([
            'message' => 'Запчастину створено',
            'item' => $item,
            'print' => $printResult,
        ]);

    }

    public function update(Request $request, KitPart $kitPart): JsonResponse
    {
        $data = $request->validate([
            'kit_id' => ['required', 'exists:kits,id'],
            'title' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:kit_parts,barcode,' . $kitPart->id],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,in_stock,sold'],
        ]);

        $printAfterSave = $request->boolean('print_after_save', false);

        $kitPart->update($data);

        $item = $kitPart->fresh()->load('kit:id,title');

        $printResult = null;

        if ($printAfterSave) {
            $printResult = app(LabelPrinterService::class)->printPartLabel($item);
        }

        return response()->json([
            'message' => 'Запчастину оновлено',
            'item' => $item,
            'print' => $printResult,
        ]);
    }

    public function destroy(KitPart $kitPart): JsonResponse
    {
        $kitPart->delete();

        return response()->json([
            'message' => 'Запчастину видалено',
        ]);
    }

    public function nextBarcode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kit_id' => ['required', 'exists:kits,id'],
        ]);

        return response()->json([
            'barcode' => $this->generateNextBarcode((int)$data['kit_id']),
        ]);
    }

    private function generateNextBarcode(int $kitId): string
    {
        $prefix = Setting::query()
            ->where('key', 'part_prefix')
            ->value('value') ?: 'PART';
        $nextNumber = ((int) KitPart::query()
                ->where('kit_id', $kitId)
                ->max('sequence_number')) + 1;

        return sprintf('%s-K%04d-P%04d', $prefix, $kitId, $nextNumber);
    }

    public function receiving(): View
    {
        return view('admin.receiving.index');
    }

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:255'],
        ]);

        $item = KitPart::query()
            ->with('kit:id,title')
            ->where('barcode', $data['barcode'])
            ->first();

        if (!$item) {
            return response()->json([
                'message' => 'Запчастину не знайдено',
            ], 404);
        }

        return response()->json([
            'item' => $item,
        ]);
    }

    public function accept(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['required', 'exists:kit_parts,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
        ]);

        $item = KitPart::query()->findOrFail($data['id']);

        if ($item->status === 'sold') {
            return response()->json([
                'message' => 'Запчастина вже продана. Прийомка неможлива.',
            ], 422);
        }

        $item->update([
            'purchase_price' => $data['purchase_price'],
            'sale_price' => $data['sale_price'],
            'status' => 'in_stock',
        ]);

        return response()->json([
            'message' => 'Запчастину прийнято на склад',
            'item' => $item->fresh()->load('kit:id,title'),
        ]);
    }
}
