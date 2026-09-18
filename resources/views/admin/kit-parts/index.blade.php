{{--resources/views/admin/kit-parts/index.blade.php--}}
@extends('admin.layouts.app')

@section('title', 'Запчастини')
@section('page_title', 'Запчастини')

@section('content')
    <div
        x-data="kitPartsCrud()"
        x-init="init()"
        class="space-y-6"
    >
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Запчастини</h1>
            </div>

            <button
                @click="openCreateModal()"
                class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-white font-semibold hover:bg-slate-800"
            >
                <span>＋</span>
                Додати запчастину
            </button>
        </div>
        <div
            x-show="toast.show"
            x-transition
            class="rounded-2xl bg-green-50 border border-green-200 p-4 text-green-700"
            style="display: none;"
        >
            <span x-text="toast.message"></span>
        </div>
        <div class="bg-white rounded-3xl border shadow-sm overflow-hidden">
            <div class="p-5 border-b flex items-center justify-between">
                <div class="font-bold">Список запчастин</div>
                <button
                    @click="fetchItems()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200"
                >
                    Оновити
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="text-left p-4">Комплект</th>
                        <th class="text-left p-4">Назва</th>
                        <th class="text-left p-4">Barcode</th>
                        <th class="text-left p-4">Закупка</th>
                        <th class="text-left p-4">Продаж</th>
                        <th class="text-left p-4">Статус</th>
                        <th class="text-right p-4">Дії</th>
                    </tr>
                    </thead>

                    <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Завантаження...
                            </td>
                        </tr>
                    </template>

                    <template x-for="item in items" :key="item.id">
                        <tr class="border-t hover:bg-slate-50">
                            <td class="p-4">
                                <span
                                    class="font-semibold text-slate-700"
                                    x-text="item.kit ? item.kit.title : '—'"
                                ></span>
                            </td>

                            <td class="p-4 font-semibold" x-text="item.title"></td>

                            <td class="p-4">
                                <span
                                    class="rounded-xl bg-slate-100 px-3 py-1 font-mono text-xs text-slate-700"
                                    x-text="item.barcode || '—'"
                                ></span>
                            </td>

                            <td class="p-4" x-text="money(item.purchase_price)"></td>
                            <td class="p-4" x-text="money(item.sale_price)"></td>

                            <td class="p-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-bold"
                                    :class="{
                                          'bg-yellow-50 text-yellow-700': item.status === 'pending',
                                          'bg-green-50 text-green-700': item.status === 'in_stock',
                                          'bg-red-50 text-red-700': item.status === 'sold'
                                          }"
                                    x-text="{
    pending: 'Очікує прийомки',
    in_stock: 'В наявності',
    sold: 'Продано'
}[item.status] || item.status"
                                ></span>
                            </td>

                            <td class="p-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="openEditModal(item)"
                                        class="rounded-xl bg-blue-50 px-3 py-2 text-blue-700 hover:bg-blue-100"
                                    >
                                        ✎
                                    </button>

                                    <button
                                        @click="deleteItem(item)"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-red-700 hover:bg-red-100"
                                    >
                                        🗑
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="!loading && items.length === 0">
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Запчастин поки немає
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div
            x-show="modal.open"
            x-transition.opacity
            class="admin-modal-backdrop"
            style="display: none;"
        >
            <div
                @click.outside="closeModal()"
                class="admin-modal"
            >
                <div class="admin-modal__header flex items-center justify-between">
                    <div>
                        <h2
                            class="text-xl font-black"
                            x-text="modal.mode === 'create' ? 'Нова запчастина' : 'Редагувати запчастину'"
                        ></h2>
                        <p class="text-sm text-slate-500">Заповніть дані запчастини</p>
                    </div>

                    <button
                        @click="closeModal()"
                        class="rounded-xl bg-slate-100 px-3 py-2 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <form
                    @submit.prevent="saveItem()"
                    class="admin-modal__body space-y-5"
                >
                    <div>
                        <label class="text-sm font-semibold text-slate-700">
                            Комплект
                        </label>

                        <select
                            x-model="form.kit_id"
                            @change="generateBarcode(false)"
                            class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                        >
                            <option value="">Оберіть комплект</option>

                            @foreach($kits as $kit)
                                <option value="{{ $kit->id }}">
                                    {{ $kit->title }}
                                </option>
                            @endforeach
                        </select>

                        <div
                            x-show="errors.kit_id"
                            class="mt-1 text-sm text-red-600"
                            x-text="errors.kit_id?.[0]"
                        ></div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">
                            Назва запчастини
                        </label>

                        <input
                            type="text"
                            x-model="form.title"
                            class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                            placeholder="Колба / Захисна резинка / Відбійник / Подушка"
                        >

                        <div
                            x-show="errors.title"
                            class="mt-1 text-sm text-red-600"
                            x-text="errors.title?.[0]"
                        ></div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">
                            Barcode
                        </label>

                        <div class="mt-2 flex gap-2">
                            <input
                                type="text"
                                x-model="form.barcode"
                                class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                                placeholder="PART-K0001-P0001"
                            >

                            <button
                                type="button"
                                @click="generateBarcode(true)"
                                class="rounded-2xl bg-slate-100 px-4 font-semibold hover:bg-slate-200"
                            >
                                ↻
                            </button>
                        </div>

                        <div
                            x-show="errors.barcode"
                            class="mt-1 text-sm text-red-600"
                            x-text="errors.barcode?.[0]"
                        ></div>
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

                    <div>
                        <label class="text-sm font-semibold text-slate-700">
                            Статус
                        </label>

                        <select
                            x-model="form.status"
                            class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                        >
                            <option value="pending">Очікує прийомки</option>
                            <option value="in_stock">В наявності</option>
                            <option value="sold">Продано</option>
                        </select>
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
                        <input
                            type="checkbox"
                            x-model="form.print_after_save"
                            class="rounded border-slate-300 text-slate-950 focus:ring-slate-950"
                        >
                        <span class="text-sm font-semibold text-slate-700">
                           <span x-text="modal.mode === 'create'
    ? 'Надрукувати етикетку після створення'
    : 'Надрукувати етикетку після оновлення'">
</span>
    </span>
                    </label>

                    <div class="admin-modal__footer flex justify-end gap-3">
                        <button
                            type="button"
                            @click="closeModal()"
                            class="rounded-2xl bg-slate-100 px-5 py-3 font-semibold hover:bg-slate-200"
                        >
                            Скасувати
                        </button>

                        <button
                            type="submit"
                            :disabled="saving"
                            class="rounded-2xl bg-slate-950 px-5 py-3 font-semibold text-white hover:bg-slate-800 disabled:opacity-50"
                        >
                            <span x-show="!saving">Зберегти</span>
                            <span x-show="saving">Збереження...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function kitPartsCrud() {
            return {
                items: [],
                loading: false,
                saving: false,
                errors: {},

                toast: {
                    show: false,
                    message: '',
                },

                modal: {
                    open: false,
                    mode: 'create',
                    id: null,
                },

                form: {
                    kit_id: '',
                    title: '',
                    barcode: '',
                    purchase_price: 0,
                    sale_price: 0,
                    status: 'pending',
                    print_after_save: true,
                },

                init() {
                    const params = new URLSearchParams(window.location.search);
                    this.fetchItems();
                    if (params.get('create') === '1') {
                        this.openCreateModal();
                        if (params.get('kit_id')) {
                            this.form.kit_id = params.get('kit_id');
                            this.generateBarcode(false);
                        }
                    }
                },

                async fetchItems() {
                    this.loading = true;

                    const params = new URLSearchParams(window.location.search);

                    const response = await fetch(`{{ route('admin.kit-parts.list') }}?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    this.items = data.items;
                    this.loading = false;
                },

                openCreateModal() {
                    this.errors = {};

                    this.modal = {
                        open: true,
                        mode: 'create',
                        id: null,
                    };

                    this.form = {
                        kit_id: '',
                        title: '',
                        barcode: '',
                        purchase_price: 0,
                        sale_price: 0,
                        status: 'pending',
                        print_after_save: true,
                    };
                },

                openEditModal(item) {
                    this.errors = {};

                    this.modal = {
                        open: true,
                        mode: 'edit',
                        id: item.id,
                    };

                    this.form = {
                        kit_id: item.kit_id,
                        title: item.title,
                        barcode: item.barcode,
                        purchase_price: item.purchase_price,
                        sale_price: item.sale_price,
                        status: item.status,
                        print_after_save: false,
                    };
                },

                closeModal() {
                    this.modal.open = false;
                },

                async saveItem() {
                    this.saving = true;
                    this.errors = {};

                    const isEdit = this.modal.mode === 'edit';

                    const url = isEdit
                        ? `/admin/kit-parts/${this.modal.id}`
                        : `{{ route('admin.kit-parts.store') }}`;

                    const method = isEdit ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        this.errors = data.errors || {};
                        this.saving = false;
                        return;
                    }
                    await this.fetchItems();
                    this.saving = false;
                    this.closeModal();
                    this.showToast(data.print?.message || data.message);
                },

                async deleteItem(item) {
                    if (!confirm(`Видалити запчастину "${item.title}"?`)) {
                        return;
                    }

                    const response = await fetch(`/admin/kit-parts/${item.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    const data = await response.json();

                    await this.fetchItems();
                    this.showToast(data.message || 'Видалено');
                },

                showToast(message) {
                    this.toast.message = message;
                    this.toast.show = true;

                    setTimeout(() => {
                        this.toast.show = false;
                    }, 2500);
                },

                money(value) {
                    return Number(value || 0).toFixed(2);
                },

                async generateBarcode(force = false) {
                    if (!this.form.kit_id) {
                        return;
                    }
                    if (this.form.barcode && !force) {
                        return;
                    }
                    const response = await fetch(`{{ route('admin.kit-parts.next-barcode') }}?kit_id=${this.form.kit_id}`, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();
                    this.form.barcode = data.barcode;
                },
            }
        }
    </script>
@endsection
