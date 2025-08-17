@extends('layouts.dashboard')

@section('title', 'Subject Management')

@section('content')
    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div id="popup-success" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Data Subject</h3>
        <a href="{{ route('subjects.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Subject
        </a>
    </div>

    <div class="table-responsive">
        <table id="subjectTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Thumbnail</th>
                    <th>Judul</th>
                    <th>Role</th>
                    <th>Jumlah Video</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($subject->thumbnail)
                                <img src="{{ $subject->thumbnail }}" alt="Thumbnail" width="100"
                                    class="rounded shadow-sm border" style="object-fit: cover; aspect-ratio: 16/9;">
                            @else
                                <span class="text-muted">Tidak ada thumbnail</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $subject->title }}</td>
                        <td>
                            @php
                                $badgeClass = match ($subject->role) {
                                    'star_seller' => 'bg-warning',
                                    'guest' => 'bg-info',
                                    'mitra_usaha' => 'bg-success',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-uppercase">
                                {{ $subject->role }}
                            </span>
                        </td>
                        <td>{{ $subject->jumlah_video ?? 0 }}</td>
                        <td>
                            <a href="{{ route('subjects.show', $subject->id) }}"
                                class="btn btn-sm btn-info text-white mb-1">
                                <i class="fas fa-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ route('subjects.edit', $subject->id) }}"
                                class="btn btn-sm btn-warning text-white mb-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus subject ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger text-white mb-1">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#subjectTable').DataTable({
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
