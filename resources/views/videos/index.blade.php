@extends('layouts.dashboard')

@section('title', 'Video Management')

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

    <div class="d-flex justify-content-between mb-3">
        <h3 class="fw-bold">Data Video</h3>
        <a href="{{ route('videos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Video
        </a>
    </div>

    <div class="table-responsive">
        <table id="videoTable" class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Thumbnail</th>
                    <th>Judul</th>
                    <th>Subject</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($videos as $video)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($video->thumbnail)
                                <img src="{{ $video->thumbnail }}" alt="Thumbnail {{ $video->title }}" width="100"
                                    class="rounded shadow-sm border" style="object-fit: cover; aspect-ratio: 16/9;">
                            @else
                                <span class="text-muted">Tidak ada thumbnail</span>
                            @endif
                        </td>
                        <td>{{ $video->title }}</td>
                        <td>{{ $video->subject->title ?? '-' }}</td>
                        <td>{{ $video->duration }}</td>
                        <td>
                            <a href="{{ route('videos.show', $video->id) }}" class="btn btn-info btn-sm text-white mb-1">
                                <i class="fas fa-eye me-1"></i> Lihat
                            </a>
                            <a href="{{ route('videos.edit', $video->id) }}" class="btn btn-warning btn-sm text-white mb-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('videos.destroy', $video->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus video ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm text-white mb-1">
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
            $('#videoTable').DataTable({
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
