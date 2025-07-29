@extends('layouts.dashboard')

@section('title', 'PDF Management')

@section('content')
    {{-- Notifikasi sukses --}}
    @if (session('success'))
       <div
            id="popup-success"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30"
        >
            <div class="bg-success text-white px-5 py-3 rounded shadow-lg text-center animate-bounce">
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

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Data PDF</h3>
        <a href="{{ route('pdfs.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah PDF
        </a>
    </div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table id="pdfTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Subject</th>
                    <th>Halaman</th>
                    <th>Link PDF</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pdfs as $pdf)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $pdf->title }}</td>
                        <td>{{ $pdf->subject->title ?? '-' }}</td>
                        <td>{{ $pdf->pages }}</td>
                        <td>
                            <a href="{{ $pdf->pdf_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-pdf me-1"></i> Lihat
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('pdfs.edit', $pdf->id) }}"
                               class="btn btn-sm btn-warning text-white mb-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('pdfs.destroy', $pdf->id) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Yakin ingin menghapus PDF ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger text-white mb-1">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada data PDF.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#pdfTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
@endpush
