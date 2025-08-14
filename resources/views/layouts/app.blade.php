<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NasaNuera LMS')</title>

    {{-- TailwindCSS via Vite --}}
    @vite('resources/css/app.css')

    {{-- Tambahan style (opsional per page) --}}
    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-lg font-semibold text-emerald-600">N A S A N U E R A</a>
        </div>
    </nav>

    {{-- Flash Message (Success) --}}
    @if (session('success'))
        <div id="popup-success" class="fixed top-4 inset-x-0 mx-auto w-fit z-50">
            <div class="bg-green-500 text-white font-medium px-6 py-2 rounded-xl shadow-lg animate-bounce text-center">
                {{ session('success') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                const popup = document.getElementById('popup-success');
                if (popup) {
                    popup.classList.add('opacity-0');
                    setTimeout(() => popup.remove(), 300);
                }
            }, 3000);
        </script>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t text-center py-4 text-sm text-gray-500">
        &copy; {{ date('Y') }} NasaNuera LMS. All rights reserved.
    </footer>

    {{-- Tambahan script (opsional per page) --}}
    @stack('scripts')
</body>

</html>
