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
            <div class="border-b bg-gradient-to-r from-slate-50 via-white to-slate-50 p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="mb-3 flex items-center gap-2">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-950 text-white shadow-sm">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="m20 20-3.5-3.5"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">Пошук запчастин</div>
                                <div class="text-xs text-slate-500">За назвою або кодом</div>
                            </div>
                        </div>

                        <form @submit.prevent="applySearch()" class="flex max-w-3xl flex-col gap-2 sm:flex-row">
                            <label class="relative block flex-1">
                                <span class="sr-only">Назва або код запчастини</span>
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="m20 20-3.5-3.5"></path>
                                </svg>
                                <input
                                    type="text"
                                    inputmode="search"
                                    x-model="search"
                                    @input.debounce.350ms="applySearch()"
                                    @keydown.escape.prevent="clearSearch()"
                                    class="h-12 w-full rounded-2xl border-slate-200 bg-white pl-12 pr-11 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-slate-900 focus:ring-slate-900"
                                    placeholder="Наприклад, колба або PART-K0001..."
                                    autocomplete="off"
                                >
                                <button
                                    x-show="search.length > 0"
                                    x-transition.opacity
                                    type="button"
                                    @click="clearSearch()"
                                    class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                    aria-label="Очистити пошук"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M18 6 6 18M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </label>

                            <button
                                type="submit"
                                class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="m20 20-3.5-3.5"></path>
                                </svg>
                                Знайти
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center justify-between gap-3 lg:justify-end">
                        <div class="text-sm text-slate-500" aria-live="polite">
                            <span x-show="!loading">
                                Знайдено: <span class="font-bold text-slate-900" x-text="items.length"></span>
                            </span>
                        </div>
                        <button
                            @click="fetchItems()"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 disabled:cursor-wait disabled:opacity-60"
                        >
                            <svg class="h-4 w-4" :class="loading ? 'animate-spin' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5"></path>
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"></path>
                            </svg>
                            Оновити
                        </button>
                    </div>
                </div>
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
                            <td colspan="7" class="p-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <path d="m20 20-3.5-3.5"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 font-bold text-slate-700" x-text="search.trim() ? 'Нічого не знайдено' : 'Запчастин поки немає'"></div>
                                <div
                                    x-show="search.trim()"
                                    class="mt-1 text-sm text-slate-400"
                                >
                                    Спробуйте змінити назву або код
                                </div>
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
                search: '',
                abortController: null,

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
                    this.search = params.get('search') || '';
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
                    if (this.abortController) {
                        this.abortController.abort();
                    }

                    const controller = new AbortController();
                    this.abortController = controller;
                    this.loading = true;

                    const params = new URLSearchParams(window.location.search);
                    const search = this.search.trim();

                    if (search) {
                        params.set('search', search);
                    } else {
                        params.delete('search');
                    }

                    try {
                        const response = await fetch(`{{ route('admin.kit-parts.list') }}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                            },
                            signal: controller.signal,
                        });

                        const data = await response.json();
                        this.items = data.items;
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            this.showToast('Не вдалося завантажити запчастини');
                        }
                    } finally {
                        if (!controller.signal.aborted) {
                            this.loading = false;
                        }
                    }
                },

                applySearch() {
                    const params = new URLSearchParams(window.location.search);
                    const search = this.search.trim();

                    if (search) {
                        params.set('search', search);
                    } else {
                        params.delete('search');
                    }

                    const query = params.toString();
                    window.history.replaceState({}, '', `${window.location.pathname}${query ? `?${query}` : ''}`);
                    this.fetchItems();
                },

                clearSearch() {
                    if (!this.search && !new URLSearchParams(window.location.search).has('search')) {
                        return;
                    }

                    this.search = '';
                    this.applySearch();
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
