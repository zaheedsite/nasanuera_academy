@extends('layouts.app')

@section('content')
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-gray-50">

        {{-- Mobile Sidebar Toggle --}}
        <div class="md:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = true" class="bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-lg shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Sidebar --}}
        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
            class="fixed z-40 inset-y-0 left-0 w-64 bg-white border-r transform md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-2xl font-bold text-emerald-600">📚 NasaNuera</h2>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-600 text-2xl">×</button>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="block py-2 px-4 rounded-lg font-medium hover:bg-emerald-100 {{ request()->routeIs('dashboard') ? 'bg-emerald-100 text-emerald-700' : 'text-gray-700' }}">🏠
                    Dashboard</a>

                {{-- Subjects --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full text-left py-2 px-4 rounded-lg hover:bg-emerald-100 font-medium text-gray-700">
                        📚 Subjects
                    </button>
                    <div x-show="open" x-transition class="pl-6 space-y-1 mt-1">
                        <a href="{{ route('subjects.index') }}" class="block text-sm hover:underline">📋 List Subjects</a>
                        <a href="{{ route('subjects.create') }}" class="block text-sm hover:underline">➕ Tambah Subject</a>
                    </div>
                </div>

                {{-- Videos --}}
                <div x-data="{ open: false }" class="mt-1">
                    <button @click="open = !open"
                        class="w-full text-left py-2 px-4 rounded-lg hover:bg-emerald-100 font-medium text-gray-700">
                        🎮 Videos
                    </button>
                    <div x-show="open" x-transition class="pl-6 space-y-1 mt-1">
                        <a href="{{ route('videos.index') }}" class="block text-sm hover:underline">📋 List Videos</a>
                        <a href="{{ route('videos.create') }}" class="block text-sm hover:underline">➕ Tambah Video</a>
                    </div>
                </div>

                {{-- PDFs --}}
                <div x-data="{ open: false }" class="mt-1">
                    <button @click="open = !open"
                        class="w-full text-left py-2 px-4 rounded-lg hover:bg-emerald-100 font-medium text-gray-700">
                        📄 PDFs
                    </button>
                    <div x-show="open" x-transition class="pl-6 space-y-1 mt-1">
                        <a href="{{ route('pdfs.index') }}" class="block text-sm hover:underline">📃 List PDFs</a>
                        <a href="{{ route('pdfs.create') }}" class="block text-sm hover:underline">➕ Tambah PDF</a>
                    </div>
                </div>
            </nav>
        </aside>

        {{-- Main --}}
        <main class="flex-1 md:ml-64 overflow-y-auto w-full">
            <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
                <p class="text-gray-600 mb-6">Selamat datang di panel admin NasaNuera LMS.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Subjects Card --}}
                    <a href="{{ route('subjects.index') }}"
                        class="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white p-6 rounded-2xl shadow hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">Total Subjects</h2>
                                <p class="text-4xl font-bold mt-2">{{ $subjectCount }}</p>
                            </div>
                            <div class="text-5xl">📚</div>
                        </div>
                    </a>

                    {{-- Videos Card --}}
                    <a href="{{ route('videos.index') }}"
                        class="bg-gradient-to-br from-indigo-400 to-indigo-600 text-white p-6 rounded-2xl shadow hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">Total Videos</h2>
                                <p class="text-4xl font-bold mt-2">{{ $videoCount }}</p>
                            </div>
                            <div class="text-5xl">▶️</div>
                        </div>
                    </a>

                    {{-- PDFs Card --}}
                    <a href="{{ route('pdfs.index') }}"
                        class="bg-gradient-to-br from-yellow-400 to-yellow-600 text-white p-6 rounded-2xl shadow hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">Total PDFs</h2>
                                <p class="text-4xl font-bold mt-2">{{ $pdfCount }}</p>
                            </div>
                            <div class="text-5xl">📄</div>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>

    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
