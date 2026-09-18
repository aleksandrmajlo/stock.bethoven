<!-- /resources/views/admin/kits/index.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Комплекти')
@section('page_title', 'Комплекти')

@section('content')
    <div
        x-data="kitsCrud()"
        x-init="init()"
        class="space-y-6"
    >
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Комплекти</h1>
            </div>

            <button
                @click="openCreateModal()"
                class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-white font-semibold hover:bg-slate-800"
            >
                <span>＋</span>
                Додати комплект
            </button>
        </div>

        <div x-show="toast.show" x-transition class="rounded-2xl bg-green-50 border border-green-200 p-4 text-green-700">
            <span x-text="toast.message"></span>
        </div>

        <div class="bg-white rounded-3xl border shadow-sm overflow-hidden">
            <div class="p-5 border-b flex items-center justify-between">
                <div class="font-bold">Список комплектів</div>

                <button
                    @click="fetchKits()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200"
                >
                    Оновити
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="text-left p-4">Назва</th>
                        <th class="text-left p-4">Закупка</th>
                        <th class="text-left p-4">Продаж</th>
                        <th class="text-left p-4">Статус</th>
                        <th class="text-left p-4">Запчастини</th>
                        <th class="text-right p-4">Дії</th>
                    </tr>
                    </thead>

                    <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                Завантаження...
                            </td>
                        </tr>
                    </template>

                    <template x-for="kit in kits" :key="kit.id">
                        <tr class="border-t hover:bg-slate-50">
                            <td class="p-4 font-semibold" x-text="kit.title"></td>
                            <td class="p-4" x-text="money(kit.purchase_price)"></td>
                            <td class="p-4" x-text="money(kit.sale_price)"></td>
                            <td class="p-4">
                            <span
                                class="inline-flex rounded-full px-3 py-1 text-xs font-bold"
                                :class="kit.status === 'in_stock'
                                    ? 'bg-green-50 text-green-700'
                                    : 'bg-red-50 text-red-700'"
                                x-text="kit.status === 'in_stock' ? 'В наявності' : 'Продано'"
                            ></span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a
                                        :href="`/admin/kit-parts?kit_id=${kit.id}`"
                                        class="rounded-xl bg-indigo-50 px-3 py-2 text-indigo-700 hover:bg-indigo-100"
                                    >
                                        🧩 <span x-text="kit.parts_count || 0"></span>
                                    </a>

                                    <a
                                        :href="`/admin/kit-parts?kit_id=${kit.id}&create=1`"
                                        class="rounded-xl bg-green-50 px-3 py-2 text-green-700 hover:bg-green-100"
                                    >
                                        ＋
                                    </a>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="openEditModal(kit)"
                                        class="rounded-xl bg-blue-50 px-3 py-2 text-blue-700 hover:bg-blue-100"
                                    >
                                        ✎
                                    </button>

                                    <button
                                        @click="deleteKit(kit)"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-red-700 hover:bg-red-100"
                                    >
                                        🗑
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="!loading && kits.length === 0">
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                Комплектів поки немає
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
            style="display: none;"
        >
            <div
                @click.outside="closeModal()"
                class="w-full max-w-xl rounded-3xl bg-white shadow-2xl"
            >
                <div class="border-b p-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black" x-text="modal.mode === 'create' ? 'Новий комплект' : 'Редагувати комплект'"></h2>
                        <p class="text-sm text-slate-500">Заповніть основні дані</p>
                    </div>

                    <button @click="closeModal()" class="rounded-xl bg-slate-100 px-3 py-2 hover:bg-slate-200">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="saveKit()" class="p-6 space-y-5">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Назва комплекту</label>
                        <input
                            type="text"
                            x-model="form.title"
                            class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                            placeholder="Пневмоподушка VW Touareg 2012 передня права"
                        >
                        <div x-show="errors.title" class="mt-1 text-sm text-red-600" x-text="errors.title?.[0]"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Ціна закупки</label>
                            <input
                                type="number"
                                step="0.01"
                                x-model="form.purchase_price"
                                class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                            >
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-700">Ціна продажу</label>
                            <input
                                type="number"
                                step="0.01"
                                x-model="form.sale_price"
                                class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">Статус</label>
                        <select
                            x-model="form.status"
                            class="mt-2 w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900"
                        >
                            <option value="in_stock">В наявності</option>
                            <option value="sold">Продано</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
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
        function kitsCrud() {
            return {
                kits: [],
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
                    title: '',
                    purchase_price: 0,
                    sale_price: 0,
                    status: 'in_stock',
                },

                init() {
                    this.fetchKits();
                },

                async fetchKits() {
                    this.loading = true;

                    const response = await fetch('{{ route('admin.kits.list') }}', {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    this.kits = data.items;
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
                        title: '',
                        purchase_price: 0,
                        sale_price: 0,
                        status: 'in_stock',
                    };
                },

                openEditModal(kit) {
                    this.errors = {};
                    this.modal = {
                        open: true,
                        mode: 'edit',
                        id: kit.id,
                    };

                    this.form = {
                        title: kit.title,
                        purchase_price: kit.purchase_price,
                        sale_price: kit.sale_price,
                        status: kit.status,
                    };
                },

                closeModal() {
                    this.modal.open = false;
                },

                async saveKit() {
                    this.saving = true;
                    this.errors = {};

                    const isEdit = this.modal.mode === 'edit';

                    const url = isEdit
                        ? `/admin/kits/${this.modal.id}`
                        : `{{ route('admin.kits.store') }}`;

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

                    await this.fetchKits();

                    this.saving = false;
                    this.closeModal();
                    this.showToast(data.message);
                },

                async deleteKit(kit) {
                    if (!confirm(`Видалити комплект "${kit.title}"?`)) {
                        return;
                    }

                    const response = await fetch(`/admin/kits/${kit.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    const data = await response.json();

                    await this.fetchKits();
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
            }
        }
    </script>
@endsection
