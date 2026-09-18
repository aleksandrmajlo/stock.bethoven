<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentSettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::query()
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.equipment.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'printer_name' => ['nullable', 'string', 'max:255'],
            'printer_ip' => ['nullable', 'string', 'max:255'],
            'printer_port' => ['nullable', 'integer'],
            'label_width' => ['required', 'integer'],
            'label_height' => ['required', 'integer'],
            'barcode_type' => ['required', 'string'],
            'scanner_mode' => ['required', 'string'],
            'part_prefix' => ['required', 'string', 'max:20'],
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Налаштування збережено');
    }
}
