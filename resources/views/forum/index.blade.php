<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Forum Diskusi - Asta Dasa Parwa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Oleo+Script:wght@400;700&display=swap" rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/forum.css') }}?v={{ time() }}">
</head>
<body class="forum-landing-page">
    <x-loading-screen />

    <!-- NAVBAR (MATCHING HOME) -->
    <x-navbar />

    <!-- HERO SECTION -->
    <header class="forum-hero">
        <h1 class="forum-hero-title">Forum Diskusi</h1>
    </header>

    <!-- MAIN CONTENT -->
    <main class="forum-main-content">
        <div class="forum-container">
            <x-content-loader />

            {{-- INLINE ALERT: Topik menunggu approve --}}
            @if(session('pending'))
            <div class="forum-alert-pending" id="alertPending">
                <div class="forum-alert-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="forum-alert-body">
                    <strong>Topik terkirim!</strong>
                    <span>Topik kamu sedang menunggu persetujuan admin sebelum bisa dilihat publik.</span>
                </div>
                <button class="forum-alert-close" onclick="this.closest('#alertPending').remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            @endif

            {{-- TOPICS LIST --}}
            <div class="topic-list">
                @forelse($topics as $topic)
                    <div class="landing-topic-wrapper">
                        <div class="landing-topic-card {{ $topic->status === 'pending' ? 'topic-card-pending' : '' }}">
                            <div class="landing-topic-content">
                                <h3>
                                    @if(auth()->check() && ($topic->user_id === auth()->id() || $topic->user_participated))
                                        <span class="dot-participated me-2" title="Pernah Berdiskusi"></span>
                                    @endif

                                    {{-- Badge PENDING hanya terlihat admin --}}
                                    @if(auth()->check() && auth()->user()->role === 'admin' && $topic->status === 'pending')
                                        <span class="badge-pending-inline me-2">
                                            <i class="bi bi-hourglass-split"></i> Menunggu
                                        </span>
                                    @endif

                                    @if($topic->status === 'approved')
                                        <a href="{{ route('forum.show', $topic->slug) }}" class="text-decoration-none text-dark">{{ $topic->title }}</a>
                                    @else
                                        {{-- Topik pending: admin bisa lihat teks, bukan link --}}
                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <span class="text-dark">{{ $topic->title }}</span>
                                        @else
                                            <a href="{{ route('forum.show', $topic->slug) }}" class="text-decoration-none text-dark">{{ $topic->title }}</a>
                                        @endif
                                    @endif
                                </h3>
                                <p>{{ Illuminate\Support\Str::limit($topic->description, 200) }}</p>
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i>{{ $topic->user->name ?? '-' }}
                                    </small>
                                @endif
                            </div>
                            
                            <div class="landing-topic-actions">
                                @if(auth()->check() && auth()->user()->role === 'admin')

                                    {{-- Tombol APPROVE (hanya muncul jika pending) --}}
                                    @if($topic->status === 'pending')
                                    <form action="{{ route('admin.forum.approve', $topic->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-approve-topic-inline" title="Setujui Topik">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Tombol DELETE --}}
                                    <form action="{{ route('admin.forum.destroy', $topic->id) }}" method="POST" class="form-delete-topic-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-delete-topic-inline" title="Hapus Topik (Admin)">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($topic->status === 'approved')
                                <a href="{{ route('forum.show', $topic->slug) }}" class="landing-topic-icon">
                                    <i class="bi bi-arrow-right-circle"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-chat-dots text-muted opacity-25" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 font-weight-bold text-muted">Belum ada topik diskusi</h5>
                        <p class="text-muted">Jadilah yang pertama memulai diskusi! Topik akan tampil setelah disetujui admin.</p>
                    </div>
                @endforelse
            </div>


            {{-- PAGINATION --}}
            <div class="mt-5 d-flex justify-content-center">
                {{ $topics->links() }}
            </div>

        </div>
    </main>

    {{-- FLOATING CREATE BUTTON --}}
    <button 
        class="btn-create-topic-landing" 
        data-bs-toggle="modal" 
        data-bs-target="#createTopicModal"
        title="Buat Topik Baru"
    >
        <i class="bi bi-plus-lg"></i>
    </button>

    {{-- MODAL CREATE TOPIC --}}
    <div class="modal fade" id="createTopicModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="modal-title fw-bold" style="color: var(--forum-red)">Buat Topik Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('forum.store-topic') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold small text-uppercase text-muted">Judul Topik</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="title" name="title" placeholder="Apa yang ingin Anda diskusikan?" required>
                        </div>

                        <div class="mb-0">
                            <label for="description" class="form-label fw-bold small text-uppercase text-muted">Deskripsi</label>
                            <textarea class="form-control rounded-3" id="description" name="description" rows="5" placeholder="Berikan detail lebih lanjut..." required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4 py-2 rounded-3" style="background-color: var(--forum-red);">Buat Topik</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>

        // SweetAlert Delete Confirmation
        document.querySelectorAll('.btn-delete-topic-inline').forEach(btn => {
            btn.addEventListener('click', function () {
                let form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Topik Diskusi?',
                    text: 'Topik ini dan semua pesan di dalamnya akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#8b0000',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    {{-- SweetAlert Success (untuk hapus topik dll) --}}
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4' }
        });
    </script>
    @endif
</body>
</html>
