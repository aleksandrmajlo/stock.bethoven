@extends('admin.layouts.app')

@section('title', 'Ремонти')
@section('page_title', 'Ремонти')

@section('content')
    <div x-data="repairOrdersCrud()" x-init="init()" class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Ремонти</h1>
                <p class="text-sm text-slate-500">Списання запчастин через сканер</p>
            </div>

            <button
                @click="openCreateModal()"
                class="rounded-2xl bg-slate-950 px-5 py-3 font-semibold text-white hover:bg-slate-800"
            >
                ＋ Нове замовлення
            </button>
        </div>

        <div
            x-show="message.text"
            x-transition
            class="rounded-2xl p-4"
            :class="message.type === 'error'
            ? 'bg-red-50 text-red-700 border border-red-200'
            : 'bg-green-50 text-green-700 border border-green-200'"
            style="display:none;"
        >
            <span x-text="message.text"></span>
        </div>

        <div class="bg-white rounded-3xl border shadow-sm overflow-hidden">
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

                    <button
                        @click="fetchOrders()"
                        :disabled="loading"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/15 disabled:cursor-wait disabled:opacity-60"
                    >
                        <svg class="h-4 w-4" :class="loading ? 'animate-spin' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5"></path>
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"></path>
                        </svg>
                        Оновити
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between border-b bg-slate-50 px-5 py-3 text-sm text-slate-500">
                <span class="font-semibold text-slate-700">Список ремонтів</span>
                <span x-show="!loading" aria-live="polite">
                    Знайдено: <b class="text-slate-900" x-text="orders.length"></b>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="text-left p-4">ID</th>
                        <th class="text-left p-4">Клієнт</th>
                        <th class="text-left p-4">Авто</th>
                        <th class="text-left p-4">Майстер</th>
                        <th class="text-left p-4">Сума</th>
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

                    <template x-for="order in orders" :key="order.id">
                        <tr class="border-t hover:bg-slate-50">
                            <td class="p-4 font-bold" x-text="'#' + order.id"></td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-800" x-text="order.client_name || '—'"></div>
                                <div class="mt-0.5 text-xs text-slate-400" x-text="order.client_phone || 'Телефон не вказано'"></div>
                            </td>
                            <td class="p-4" x-text="order.car_info || '—'"></td>
                            <td class="p-4" x-text="order.master_name || '—'"></td>
                            <td class="p-4 font-bold" x-text="money(order.total_amount)"></td>
                            <td class="p-4">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="statusClass(order.status)"
                                x-text="statusLabel(order.status)"
                            ></span>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="openOrder(order)"
                                        class="rounded-xl bg-blue-50 px-3 py-2 text-blue-700 hover:bg-blue-100"
                                    >
                                        Сканер
                                    </button>

                                    <button
                                        @click="confirmPayment(order)"
                                        x-show="order.status === 'payment_due'"
                                        class="rounded-xl bg-green-50 px-3 py-2 text-green-700 hover:bg-green-100"
                                    >
                                        Оплата
                                    </button>

                                    <button
                                        @click="deleteOrder(order)"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-red-700 hover:bg-red-100"
                                    >
                                        🗑
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="!loading && orders.length === 0">
                        <tr>
                            <td colspan="7" class="p-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <path d="m20 20-3.5-3.5"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 font-bold text-slate-700" x-text="search.trim() ? 'Ремонтів не знайдено' : 'Замовлень поки немає'"></div>
                                <div x-show="search.trim()" class="mt-1 text-sm text-slate-400">
                                    Перевірте запит або спробуйте інші дані
                                </div>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            x-show="createModal"
            x-transition.opacity
            class="admin-modal-backdrop"
            style="display:none;"
        >
            <div @click.outside="createModal = false" class="admin-modal">
                <div class="admin-modal__header flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black">Нове ремонтне замовлення</h2>
                        <p class="text-sm text-slate-500">Дані клієнта та майстра</p>
                    </div>

                    <button @click="createModal = false" class="rounded-xl bg-slate-100 px-3 py-2 hover:bg-slate-200">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="createOrder()" class="admin-modal__body space-y-5">
                    <div>
                        <label class="text-sm font-semibold">Клієнт</label>
                        <input x-model="form.client_name" class="mt-2 w-full rounded-2xl border-slate-300">
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Телефон</label>
                        <input x-model="form.client_phone" class="mt-2 w-full rounded-2xl border-slate-300">
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Авто</label>
                        <input x-model="form.car_info" placeholder="VW Touareg 2012" class="mt-2 w-full rounded-2xl border-slate-300">
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Майстер</label>
                        <input x-model="form.master_name" class="mt-2 w-full rounded-2xl border-slate-300">
                    </div>

                    <div class="admin-modal__footer flex justify-end gap-3">
                        <button type="button" @click="createModal = false" class="rounded-2xl bg-slate-100 px-5 py-3 font-semibold">
                            Скасувати
                        </button>

                        <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 font-semibold text-white">
                            Створити
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            x-show="scannerModal"
            x-transition.opacity
            class="admin-modal-backdrop"
            style="display:none;"
        >
            <div @click.outside="closeScanner()" class="admin-modal">
                <div class="admin-modal__header flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black">
                            Списання по ремонту <span x-text="activeOrder ? '#' + activeOrder.id : ''"></span>
                        </h2>
                        <p class="text-sm text-slate-500">Скануйте barcode запчастини</p>
                    </div>

                    <button @click="closeScanner()" class="rounded-xl bg-slate-100 px-3 py-2 hover:bg-slate-200">
                        ✕
                    </button>
                </div>

                <div class="admin-modal__body space-y-5">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <label class="text-sm font-semibold">Поле сканера</label>
                        <input
                            x-ref="scannerInput"
                            x-model="barcode"
                            @keydown.enter.prevent="scanPart()"
                            placeholder="Скануйте barcode..."
                            class="mt-2 w-full rounded-2xl border-slate-300 text-xl font-mono"
                            autocomplete="off"
                        >
                    </div>

                    <template x-if="activeOrder">
                        <div class="rounded-2xl border p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-slate-500">Сума</div>
                                    <div class="text-3xl font-black" x-text="money(activeOrder.total_amount)"></div>
                                </div>

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold"
                                    :class="statusClass(activeOrder.status)"
                                    x-text="statusLabel(activeOrder.status)"
                                ></span>
                            </div>
                        </div>
                    </template>

                    <div class="rounded-2xl border overflow-hidden">
                        <div class="border-b bg-slate-50 p-4 font-bold">Списані запчастини</div>

                        <template x-if="activeOrder && activeOrder.items.length === 0">
                            <div class="p-6 text-center text-slate-400">
                                Запчастин ще немає
                            </div>
                        </template>

                        <template x-for="part in activeOrder?.items || []" :key="part.id">
                            <div class="border-b p-4 flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-bold" x-text="part.title"></div>
                                    <div class="font-mono text-xs text-slate-500" x-text="part.barcode"></div>
                                </div>

                                <div class="font-bold" x-text="money(part.sale_price)"></div>
                            </div>
                        </template>
                    </div>

                    <div class="admin-modal__footer flex justify-end gap-3">
                        <button @click="closeScanner()" class="rounded-2xl bg-slate-100 px-5 py-3 font-semibold">
                            Закрити
                        </button>

                        <button
                            @click="confirmPayment(activeOrder)"
                            x-show="activeOrder && activeOrder.status === 'payment_due'"
                            class="rounded-2xl bg-green-600 px-5 py-3 font-semibold text-white"
                        >
                            Підтвердити оплату
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function repairOrdersCrud() {
            return {
                orders: [],
                activeOrder: null,
                loading: false,
                createModal: false,
                scannerModal: false,
                barcode: '',
                search: '',
                abortController: null,

                form: {
                    client_name: '',
                    client_phone: '',
                    car_info: '',
                    master_name: '',
                },

                message: {
                    type: '',
                    text: '',
                },

                init() {
                    const params = new URLSearchParams(window.location.search);
                    this.search = params.get('search') || '';
                    this.fetchOrders();
                },

                async fetchOrders() {
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
                        const response = await fetch(`{{ route('admin.repair-orders.list') }}?${params.toString()}`, {
                            headers: { 'Accept': 'application/json' },
                            signal: controller.signal,
                        });

                        if (!response.ok) {
                            throw new Error('Search request failed');
                        }

                        const data = await response.json();
                        this.orders = data.items;
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            this.showMessage('Не вдалося завантажити ремонти', 'error');
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
                    this.fetchOrders();
                },

                clearSearch() {
                    if (!this.search && !new URLSearchParams(window.location.search).has('search')) {
                        return;
                    }

                    this.search = '';
                    this.applySearch();
                },

                openCreateModal() {
                    this.form = {
                        client_name: '',
                        client_phone: '',
                        car_info: '',
                        master_name: '',
                    };

                    this.createModal = true;
                },

                async createOrder() {
                    const response = await fetch('{{ route('admin.repair-orders.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(this.form),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.showMessage(data.message || 'Помилка створення', 'error');
                        return;
                    }

                    this.createModal = false;
                    await this.fetchOrders();
                    this.openOrder(data.item);
                    this.showMessage(data.message, 'success');
                },

                openOrder(order) {
                    this.activeOrder = order;
                    this.scannerModal = true;
                    this.barcode = '';

                    this.$nextTick(() => {
                        this.$refs.scannerInput?.focus();
                    });
                },

                closeScanner() {
                    this.scannerModal = false;
                    this.activeOrder = null;
                    this.barcode = '';
                },

                async scanPart() {
                    if (!this.activeOrder || !this.barcode.trim()) {
                        return;
                    }

                    const response = await fetch(`/admin/repair-orders/${this.activeOrder.id}/scan`, {
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

                    this.barcode = '';

                    if (!response.ok) {
                        this.showMessage(data.message || 'Помилка сканування', 'error');
                        this.$nextTick(() => this.$refs.scannerInput?.focus());
                        return;
                    }

                    this.activeOrder = data.item;
                    await this.fetchOrders();

                    this.showMessage(data.message, 'success');

                    this.$nextTick(() => this.$refs.scannerInput?.focus());
                },

                async confirmPayment(order) {
                    if (!order) return;

                    const response = await fetch(`/admin/repair-orders/${order.id}/confirm-payment`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.showMessage(data.message || 'Помилка підтвердження', 'error');
                        return;
                    }

                    this.activeOrder = data.item;
                    await this.fetchOrders();

                    this.showMessage(data.message, 'success');
                },

                async deleteOrder(order) {
                    if (!confirm(`Видалити ремонт #${order.id}?`)) return;

                    const response = await fetch(`/admin/repair-orders/${order.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    const data = await response.json();

                    await this.fetchOrders();
                    this.showMessage(data.message || 'Видалено', 'success');
                },

                showMessage(text, type = 'success') {
                    this.message = { text, type };

                    setTimeout(() => {
                        this.message = { text: '', type: '' };
                    }, 3000);
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
