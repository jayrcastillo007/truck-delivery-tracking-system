<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Tracking System')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="bg-slate-100 text-slate-800 overflow-x-hidden">

    @include('components.sidebar')

    <div class="ml-64 min-w-0">
        <header class="sticky top-0 z-30 bg-white border-b border-slate-200">
            <div class="h-16 px-6 flex items-center justify-between">
                <div class="leading-tight">
                    <h1 class="text-base sm:text-lg font-semibold text-slate-900">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    <p class="text-xs text-slate-500 hidden sm:block">Thesis Tracking System</p>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition text-sm shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <div class="px-6 pt-6">
            <x-messages />
        </div>

        <main class="px-6 pb-10 pt-4">
            @yield('content')
        </main>
    </div>

</body>

</html>
