@extends('admin.layouts.app')

@section('title', 'Прийомка')
@section('page_title', 'Прийомка')

@section('content')
    <div
        x-data="receivingScanner()"
        x-init="init()"
        class="space-y-6"
    >
        <div class="rounded-3xl bg-slate-950 p-6 text-white">
            <div class="text-sm text-slate-300">Сканер Netum NT-1228BL</div>
            <h1 class="mt-1 text-2xl font-black">Прийомка запчастин на склад</h1>
            <p class="mt-2 text-slate-300">
                Відскануйте barcode з етикетки. Сканер працює як клавіатура.
            </p>
        </div>

        <div
            x-show="message.text"
            x-transition
            class="rounded-2xl p-4"
            :class="message.type === 'error'
            ? 'bg-red-50 text-red-700 border border-red-200'
            : 'bg-green-50 text-green-700 border border-green-200'"
            style="display: none;"
        >
            <span x-text="message.text"></span>
        </div>

        <div class="bg-white rounded-3xl border shadow-sm p-6">
            <label class="text-sm font-semibold text-slate-700">
                Поле сканування
            </label>

            <div class="mt-2 flex gap-3">
                <input
                    x-ref="scannerInput"
                    x-model="barcode"
                    @keydown.enter.prevent="scan()"
                    type="text"
                    class="w-full rounded-2xl border-slate-300 text-xl font-mono focus:border-slate-900 focus:ring-slate-900"
                    placeholder="Скануйте barcode..."
                    autocomplete="off"
                >

                <button
                    @click="scan()"
                    :disabled="loading"
                    class="rounded-2xl bg-slate-950 px-6 font-semibold text-white hover:bg-slate-800 disabled:opacity-50"
                >
                    Сканувати
                </button>
            </div>

            <p class="mt-3 text-sm text-slate-500">
                Після сканування натиснеться Enter автоматично.
            </p>
        </div>

        <template x-if="item">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-3xl border shadow-sm p-6 space-y-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm text-slate-500">Знайдена запчастина</div>
                            <h2 class="mt-1 text-2xl font-black" x-text="item.title"></h2>
                        </div>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-bold"
                            :class="{
                            'bg-yellow-50 text-yellow-700': item.status === 'pending',
                            'bg-green-50 text-green-700': item.status === 'in_stock',
                            'bg-red-50 text-red-700': item.status === 'sold',
                        }"
                            x-text="statusLabel(item.status)"
                        ></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-slate-500">Комплект</div>
                            <div class="mt-1 font-bold" x-text="item.kit ? item.kit.title : '—'"></div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-slate-500">Barcode</div>
                            <div class="mt-1 font-mono font-bold" x-text="item.barcode"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">
                                Ціна закупки
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                x-model="form.purchase_price"
                                class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                            >
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-700">
                                Ціна продажу
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                x-model="form.sale_price"
                                class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                            >
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            @click="resetScan()"
                            class="rounded-2xl bg-slate-100 px-5 py-3 font-semibold hover:bg-slate-200"
                        >
                            Скасувати
                        </button>

                        <button
                            @click="accept()"
                            :disabled="saving || item.status === 'sold'"
                            class="rounded-2xl bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700 disabled:opacity-50"
                        >
                            Прийняти на склад
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border shadow-sm p-6">
                    <h3 class="font-black">Підказка</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <p>1. Скануйте barcode.</p>
                        <p>2. Перевірте назву та комплект.</p>
                        <p>3. Вкажіть закупку та продаж.</p>
                        <p>4. Натисніть “Прийняти на склад”.</p>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function receivingScanner() {
            return {
                barcode: '',
                item: null,
                loading: false,
                saving: false,

                form: {
                    purchase_price: 0,
                    sale_price: 0,
                },

                message: {
                    type: '',
                    text: '',
                },

                init() {
                    this.focusScanner();

                    window.addEventListener('click', () => {
                        this.focusScanner();
                    });
                },

                focusScanner() {
                    this.$nextTick(() => {
                        this.$refs.scannerInput?.focus();
                    });
                },

                async scan() {
                    if (!this.barcode.trim()) {
                        return;
                    }

                    this.loading = true;
                    this.message = { type: '', text: '' };

                    const response = await fetch('{{ route('admin.receiving.scan') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            barcode: this.barcode.trim(),
                        }),
                    });

                    const data = await response.json();

                    this.loading = false;

                    if (!response.ok) {
                        this.item = null;
                        this.message = {
                            type: 'error',
                            text: data.message || 'Помилка сканування',
                        };
                        this.barcode = '';
                        this.focusScanner();
                        return;
                    }

                    this.item = data.item;
                    this.form.purchase_price = data.item.purchase_price || 0;
                    this.form.sale_price = data.item.sale_price || 0;

                    this.message = {
                        type: 'success',
                        text: 'Запчастину знайдено',
                    };
                },

                async accept() {
                    if (!this.item) {
                        return;
                    }

                    this.saving = true;

                    const response = await fetch('{{ route('admin.receiving.accept') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            id: this.item.id,
                            purchase_price: this.form.purchase_price,
                            sale_price: this.form.sale_price,
                        }),
                    });

                    const data = await response.json();

                    this.saving = false;

                    if (!response.ok) {
                        this.message = {
                            type: 'error',
                            text: data.message || 'Помилка прийомки',
                        };
                        return;
                    }

                    this.item = data.item;
                    this.message = {
                        type: 'success',
                        text: data.message,
                    };

                    this.barcode = '';
                    this.focusScanner();
                },

                resetScan() {
                    this.item = null;
                    this.barcode = '';
                    this.message = { type: '', text: '' };
                    this.focusScanner();
                },

                statusLabel(status) {
                    return {
                        pending: 'Очікує прийомки',
                        in_stock: 'В наявності',
                        sold: 'Продано',
                    }[status] || status;
                },
            }
        }
    </script>
@endsection
