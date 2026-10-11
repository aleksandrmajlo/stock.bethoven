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

    <div
        x-data="dashboardRepairSearch()"
        x-init="init()"
        class="mt-8 overflow-hidden rounded-3xl border bg-white shadow-sm"
    >
        <div class="border-b bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800 p-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/15">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-3.5-3.5"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black">Пошук ремонтів</h2>
                            <p class="text-sm text-slate-300">Клієнт, телефон, авто або майстер</p>
                        </div>
                    </div>

                    <form @submit.prevent="applySearch()" class="flex max-w-4xl flex-col gap-2 sm:flex-row">
                        <label class="relative block flex-1">
                            <span class="sr-only">Пошук ремонтних замовлень</span>
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
                                class="h-12 w-full rounded-2xl border-white/20 bg-white pl-12 pr-11 text-sm text-slate-900 shadow-lg placeholder:text-slate-400 focus:border-white focus:ring-white"
                                placeholder="Наприклад, Олександр, +380..., Touareg або Іван..."
                                autocomplete="off"
                            >
                            <button
                                x-show="search.length > 0"
                                x-transition.opacity
                                type="button"
                                @click="clearSearch()"
                                class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                aria-label="Очистити пошук ремонтів"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M18 6 6 18M6 6l12 12"></path>
                                </svg>
                            </button>
                        </label>

                        <button
                            type="submit"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-bold text-slate-950 shadow-lg transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-3.5-3.5"></path>
                            </svg>
                            Знайти ремонт
                        </button>
                    </form>
                </div>

                <a
                    href="{{ route('admin.repair-orders.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/15"
                >
                    Усі ремонти
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div x-show="loading" class="p-10 text-center text-sm text-slate-500">
            <svg class="mx-auto mb-3 h-6 w-6 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5"></path>
                <path d="M4 13a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"></path>
            </svg>
            Шукаємо ремонти...
        </div>

        <div x-show="error && !loading" class="border-b border-red-100 bg-red-50 px-6 py-4 text-sm font-semibold text-red-700" x-text="error"></div>

        <div x-show="!hasSearched && !loading" class="p-10 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="m22 21-3-3m1-3a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"></path>
                </svg>
            </div>
            <div class="mt-3 font-bold text-slate-700">Знайдіть потрібний ремонт</div>
            <div class="mt-1 text-sm text-slate-400">Введіть ім’я, телефон, автомобіль або майстра</div>
        </div>

        <div x-show="hasSearched && !loading && !error">
            <div class="flex items-center justify-between border-b bg-slate-50 px-6 py-3 text-sm text-slate-500">
                <span>Результати пошуку</span>
                <span>Знайдено: <b class="text-slate-900" x-text="orders.length"></b></span>
            </div>

            <div x-show="orders.length > 0" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white text-slate-500">
                    <tr>
                        <th class="p-4 text-left">Ремонт</th>
                        <th class="p-4 text-left">Клієнт</th>
                        <th class="p-4 text-left">Авто</th>
                        <th class="p-4 text-left">Майстер</th>
                        <th class="p-4 text-left">Сума</th>
                        <th class="p-4 text-left">Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="order in orders" :key="order.id">
                        <tr class="border-t transition hover:bg-slate-50">
                            <td class="p-4 font-black text-slate-900" x-text="'#' + order.id"></td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-800" x-text="order.client_name || '—'"></div>
                                <div class="mt-0.5 text-xs text-slate-400" x-text="order.client_phone || 'Телефон не вказано'"></div>
                            </td>
                            <td class="p-4 text-slate-700" x-text="order.car_info || '—'"></td>
                            <td class="p-4 text-slate-700" x-text="order.master_name || '—'"></td>
                            <td class="p-4 font-bold text-slate-900" x-text="money(order.total_amount)"></td>
                            <td class="p-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-bold"
                                    :class="statusClass(order.status)"
                                    x-text="statusLabel(order.status)"
                                ></span>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>

            <div x-show="orders.length === 0" class="p-10 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </div>
                <div class="mt-3 font-bold text-slate-700">Ремонтів не знайдено</div>
                <div class="mt-1 text-sm text-slate-400">Перевірте запит або спробуйте інші дані</div>
            </div>
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

    <script>
        function dashboardRepairSearch() {
            return {
                search: '',
                orders: [],
                loading: false,
                hasSearched: false,
                error: '',
                abortController: null,

                init() {
                    const params = new URLSearchParams(window.location.search);
                    this.search = params.get('search') || '';

                    if (this.search.trim()) {
                        this.fetchOrders();
                    }
                },

                applySearch() {
                    const params = new URLSearchParams(window.location.search);
                    const search = this.search.trim();

                    if (!search) {
                        params.delete('search');
                        this.abortController?.abort();
                        this.orders = [];
                        this.loading = false;
                        this.hasSearched = false;
                        this.error = '';
                        this.replaceUrl(params);
                        return;
                    }

                    params.set('search', search);
                    this.replaceUrl(params);
                    this.fetchOrders();
                },

                clearSearch() {
                    this.search = '';
                    this.applySearch();
                },

                async fetchOrders() {
                    const search = this.search.trim();

                    if (!search) {
                        return;
                    }

                    this.abortController?.abort();

                    const controller = new AbortController();
                    this.abortController = controller;
                    this.loading = true;
                    this.hasSearched = true;
                    this.error = '';

                    const params = new URLSearchParams({ search });

                    try {
                        const response = await fetch(`{{ route('admin.repair-orders.list') }}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                            },
                            signal: controller.signal,
                        });

                        if (!response.ok) {
                            throw new Error('Search request failed');
                        }

                        const data = await response.json();
                        this.orders = data.items;
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            this.orders = [];
                            this.error = 'Не вдалося виконати пошук. Спробуйте ще раз.';
                        }
                    } finally {
                        if (!controller.signal.aborted) {
                            this.loading = false;
                        }
                    }
                },

                replaceUrl(params) {
                    const query = params.toString();
                    window.history.replaceState({}, '', `${window.location.pathname}${query ? `?${query}` : ''}`);
                },

                money(value) {
                    return Number(value || 0).toFixed(2);
                },

                statusLabel(status) {
                    return {
                        draft: 'Чернетка',
                        payment_due: 'Очікує оплату',
                        paid: 'Оплачено',
                        cancelled: 'Скасовано',
                    }[status] || status;
                },

                statusClass(status) {
                    return {
                        draft: 'bg-slate-100 text-slate-700',
                        payment_due: 'bg-yellow-50 text-yellow-700',
                        paid: 'bg-green-50 text-green-700',
                        cancelled: 'bg-red-50 text-red-700',
                    }[status] || 'bg-slate-100 text-slate-700';
                },
            }
        }
    </script>
@endsection
