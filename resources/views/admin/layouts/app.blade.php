<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Адмін панель')</title>

    @vite([
        'resources/css/app.css',
        'resources/scss/admin/app.scss',
        'resources/js/app.js'
    ])
</head>

<body class="bg-slate-100 text-slate-900">
<div x-data="{ sidebarOpen: true }" class="min-h-screen flex">

    <aside
        :class="sidebarOpen ? 'w-72' : 'w-20'"
        class="bg-slate-950 text-white transition-all duration-300 flex flex-col"
    >
        <div class="h-20 px-5 flex items-center justify-between border-b border-white/10">
            <div x-show="sidebarOpen" class="text-xl font-black tracking-tight">
                SaaS Admin
            </div>

            <button
                @click="sidebarOpen = !sidebarOpen"
                class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center"
            >
                ☰
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-white/10 transition">
                <span>📊</span>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('admin.kits.index') }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-white/10 transition">
                <span>📦</span>
                <span x-show="sidebarOpen">Комплекти</span>
            </a>
            <a href="{{ route('admin.kit-parts.index') }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-white/10 transition">
                <span>🧩</span>
                <span x-show="sidebarOpen">Запчастини</span>
            </a>

            <a href="{{ route('admin.receiving.index') }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-white/10 transition">
                <span>📥</span>
                <span x-show="sidebarOpen">Прийомка</span>
            </a>

            <a href="{{ route('admin.repair-orders.index') }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-white/10 transition">
                <span>🧾</span>
                <span x-show="sidebarOpen">Ремонти</span>
            </a>

            <a href="{{ route('admin.equipment.index') }}"
               class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-white/10 transition">
                <span>🖨️</span>
                <span x-show="sidebarOpen">Обладнання</span>
            </a>

        </nav>
    </aside>

    <main class="flex-1">
        <header class="h-20 bg-white border-b px-8 flex justify-between items-center">
            <div>
                <div class="text-sm text-slate-400">Адмін панель</div>
                <div class="text-xl font-bold">
                    @yield('page_title', 'Панель керування')
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-4 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold">
                    Вийти
                </button>
            </form>
        </header>

        <section class="p-8">
            @yield('content')
        </section>
    </main>

</div>
</body>
</html>
