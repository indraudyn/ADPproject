<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $topic->title }} - Forum Diskusi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&family=Oleo+Script:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/forum.css') }}">
</head>
<body class="forum-show-page">
    <x-loading-screen />

    <!-- HERO SECTION -->
    <header class="show-hero">
        <a href="{{ route('forum.index') }}" class="back-link-show">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h1 class="show-hero-title">{{ $topic->title }}</h1>
        <p class="show-hero-subtitle">{{ Illuminate\Support\Str::limit($topic->description, 100) }}</p>
    </header>

    <!-- CHAT CONTAINER -->
    <div class="chat-container-show">
        <div class="chat-card-v2">
            
            <div class="chat-body-v2">
                @forelse($messages as $message)
                    @php $isMe = (auth()->id() === $message->user_id); @endphp
                    
                    <div class="bubble-v2 {{ $isMe ? 'bubble-me-v2' : 'bubble-other-v2' }}">
                        <span class="bubble-name-v2">{{ $isMe ? 'Anda' : $message->user->name }}</span>
                        <div class="bubble-content-v2">{{ $message->message }}</div>
                        
                        <div class="bubble-footer-v2">
                            <span class="bubble-time-v2">{{ $message->created_at->format('H:i') }}</span>
                            
                            @if($isMe)
                            <form action="{{ route('forum.destroy', $message->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 border-0 bubble-delete-v2" onclick="return confirm('Hapus pesan ini?')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 opacity-50">
                        <p>Belum ada diskusi. Jadilah yang pertama!</p>
                    </div>
                @endforelse
            </div>

            <!-- CHAT FOOTER -->
            @auth
            <form action="{{ route('forum.store') }}" method="POST">
                @csrf
                <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                <div class="chat-footer-v2">
                    <input type="text" name="message" class="chat-input-v2" placeholder="Write message" required>
                    
                    <div class="chat-footer-icons">
                        <i class="bi bi-paperclip"></i>
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                    <button type="submit" class="btn-send-v2">
                        Send <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </form>
            @else
            <div class="chat-footer-v2 justify-content-center">
                <p class="mb-0 text-muted small">Silakan <a href="{{ route('login') }}" class="fw-bold text-danger">login</a> untuk membalas.</p>
            </div>
            @endauth
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatBody = document.querySelector(".chat-body-v2");
            if (chatBody) {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        });
    </script>
</body>
</html>
