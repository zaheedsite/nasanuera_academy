@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-3xl w-full max-w-4xl p-10">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
                {{ $submit_label ?? 'Tambah PDF' }}
            </h2>

            <form action="{{ $form_action }}" method="POST" class="space-y-6">
                @csrf
                @if ($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Judul PDF --}}
                    <div>
                        <label for="title" class="block font-semibold text-gray-700 mb-1">Judul PDF</label>
                        <input type="text" name="title" id="title"
                            value="{{ old('title', $pdf->title ?? '') }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                            required>
                    </div>

                    {{-- Jumlah Halaman --}}
                    <div>
                        <label for="pages" class="block font-semibold text-gray-700 mb-1">Jumlah Halaman</label>
                        <input type="number" name="pages" id="pages" min="1"
                            value="{{ old('pages', $pdf->pages ?? 1) }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                            required>
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label for="subject_id" class="block font-semibold text-gray-700 mb-1">Subject</label>
                        <select name="subject_id" id="subject_id"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                            required>
                            <option value="">-- Pilih Subject --</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                    {{ old('subject_id', $pdf->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PDF URL --}}
                    <div>
                        <label for="pdf_url" class="block font-semibold text-gray-700 mb-1">PDF URL (dari Cloud)</label>
                        <input type="url" name="pdf_url" id="pdf_url"
                            value="{{ old('pdf_url', $pdf->pdf_url ?? '') }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                            required>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex justify-end space-x-4 pt-6">
                    <button type="submit"
                        class="px-6 py-2 rounded-lg text-white font-semibold bg-emerald-600 hover:bg-emerald-700 transition duration-200">
                        {{ $submit_label ?? 'Simpan' }}
                    </button>
                    <a href="{{ route('pdfs.index') }}"
                        class="px-6 py-2 rounded-lg text-white font-semibold bg-gray-400 hover:bg-gray-500 transition duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
