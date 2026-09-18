@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="mb-8 rounded-3xl bg-gradient-to-r from-slate-950 to-slate-800 text-white p-8 shadow-lg">
        <div class="text-sm text-slate-300">Складський облік</div>
        <h1 class="mt-2 text-3xl font-black">Керування комплектами запчастин</h1>
        <p class="mt-3 text-slate-300 max-w-2xl">
            CRM для прийомки, маркування, продажу та списання запчастин через Barcode.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">Комплекти</div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center">📦</div>
            </div>
            <div class="mt-4 text-4xl font-black">{{ $kitsTotal }}</div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">В наявності</div>
                <div class="w-11 h-11 rounded-2xl bg-green-50 flex items-center justify-center">✅</div>
            </div>
            <div class="mt-4 text-4xl font-black">{{ $partsInStock }}</div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">Продано</div>
                <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center">💰</div>
            </div>
            <div class="mt-4 text-4xl font-black">{{ $partsSold }}</div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-3xl border bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">🖨️</div>
                <div>
                    <h2 class="text-lg font-black">Обладнання</h2>
                    <p class="text-sm text-slate-500">Принтер, сканер, етикетки</p>
                </div>
            </div>

            <div class="mt-5 space-y-3 text-sm text-slate-700">
                <div class="flex gap-3">
                    <span class="text-slate-400">•</span>
                    <span>Принтер: <b>Xprinter XP-420B</b></span>
                </div>
                <div class="flex gap-3">
                    <span class="text-slate-400">•</span>
                    <span>Сканер: <b>Netum NT-1228BL</b></span>
                </div>
                <div class="flex gap-3">
                    <span class="text-slate-400">•</span>
                    <span>Етикетки: <b>58x40 мм</b></span>
                </div>
                <div class="flex gap-3">
                    <span class="text-slate-400">•</span>
                    <span>Штрихкод: <b>Code128 Barcode</b></span>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-yellow-50 flex items-center justify-center">🏷️</div>
                <div>
                    <h2 class="text-lg font-black">Статуси запчастин</h2>
                    <p class="text-sm text-slate-500">Що означає кожен статус</p>
                </div>
            </div>

            <div class="mt-5 space-y-3 text-sm">
                <div class="rounded-2xl bg-yellow-50 p-4 text-yellow-800">
                    <b>pending</b> — створено, етикетка надрукована, чекає сканування.
                </div>
                <div class="rounded-2xl bg-green-50 p-4 text-green-800">
                    <b>in_stock</b> — прийнято на склад після сканера.
                </div>
                <div class="rounded-2xl bg-red-50 p-4 text-red-800">
                    <b>sold</b> — продано або списано в ремонт.
                </div>
            </div>
        </div>

        <div class="rounded-3xl border bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">📥</div>
                <div>
                    <h2 class="text-lg font-black">Прийомка</h2>
                    <p class="text-sm text-slate-500">Як запчастина потрапляє на склад</p>
                </div>
            </div>

            <div class="mt-5 space-y-3 text-sm text-slate-700">
                <p>1. Створюємо запчастину — статус <b>pending</b>.</p>
                <p>2. Друкуємо етикетку і клеїмо її на запчастину.</p>
                <p>3. Відкриваємо сторінку <b>Прийомка</b>.</p>
                <p>4. Скануємо Barcode.</p>
                <p>5. Перевіряємо закупку і продаж.</p>
                <p>6. Натискаємо <b>Прийняти на склад</b>.</p>
                <p>7. Статус стає <b>in_stock</b>.</p>
            </div>
        </div>

        <div class="rounded-3xl border bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center">🧾</div>
                <div>
                    <h2 class="text-lg font-black">Ремонт і списання</h2>
                    <p class="text-sm text-slate-500">Як запчастина продається</p>
                </div>
            </div>

            <div class="mt-5 space-y-3 text-sm text-slate-700">
                <p>1. Клієнт приїхав на ремонт.</p>
                <p>2. Створюємо ремонтне замовлення.</p>
                <p>3. Скануємо Barcode запчастини.</p>
                <p>4. Якщо статус <b>in_stock</b> — система додає її в реалізацію.</p>
                <p>5. Запчастина отримує статус <b>sold</b>.</p>
                <p>6. Майстер повинен здати гроші.</p>
                <p>7. Адмін підтверджує оплату.</p>
            </div>
        </div>
    </div>
@endsection
