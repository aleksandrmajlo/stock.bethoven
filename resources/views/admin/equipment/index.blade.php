@extends('admin.layouts.app')

@section('title', 'Обладнання')
@section('page_title', 'Обладнання')

@section('content')
    <div class="max-w-4xl space-y-6">

        @if(session('success'))
            <div class="rounded-2xl bg-green-50 border border-green-200 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.equipment.update') }}" class="bg-white rounded-3xl border shadow-sm p-6 space-y-6">
            @csrf

            <div>
                <h2 class="text-xl font-black">Принтер етикеток</h2>
                <p class="text-sm text-slate-500">Xprinter XP-420B / 58x40 мм / Code128</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm font-semibold">Назва принтера</label>
                    <input name="printer_name" value="{{ $settings['printer_name'] ?? 'Xprinter XP-420B' }}"
                           class="mt-2 w-full rounded-2xl border-slate-300">
                </div>

                <div>
                    <label class="text-sm font-semibold">IP адреса принтера</label>
                    <input name="printer_ip" value="{{ $settings['printer_ip'] ?? '' }}"
                           placeholder="192.168.1.100"
                           class="mt-2 w-full rounded-2xl border-slate-300">
                </div>

                <div>
                    <label class="text-sm font-semibold">Порт</label>
                    <input name="printer_port" type="number" value="{{ $settings['printer_port'] ?? 9100 }}"
                           class="mt-2 w-full rounded-2xl border-slate-300">
                </div>

                <div>
                    <label class="text-sm font-semibold">Тип barcode</label>
                    <select name="barcode_type" class="mt-2 w-full rounded-2xl border-slate-300">
                        <option value="C128" selected>Code128</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold">Ширина етикетки, мм</label>
                    <input name="label_width" type="number" value="{{ $settings['label_width'] ?? 58 }}"
                           class="mt-2 w-full rounded-2xl border-slate-300">
                </div>

                <div>
                    <label class="text-sm font-semibold">Висота етикетки, мм</label>
                    <input name="label_height" type="number" value="{{ $settings['label_height'] ?? 40 }}"
                           class="mt-2 w-full rounded-2xl border-slate-300">
                </div>
            </div>

            <div class="border-t pt-6">
                <h2 class="text-xl font-black">Сканер</h2>
                <p class="text-sm text-slate-500">Netum NT-1228BL працює як клавіатура</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label class="text-sm font-semibold">Режим сканера</label>
                        <select name="scanner_mode" class="mt-2 w-full rounded-2xl border-slate-300">
                            <option value="keyboard" selected>Keyboard HID</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Префікс запчастини</label>
                        <input name="part_prefix" value="{{ $settings['part_prefix'] ?? 'PART' }}"
                               class="mt-2 w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="rounded-2xl bg-slate-950 px-6 py-3 font-semibold text-white hover:bg-slate-800">
                    Зберегти налаштування
                </button>
            </div>
        </form>
    </div>
@endsection
