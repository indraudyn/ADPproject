<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
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
        <x-content-loader />
        <div class="chat-card-v2">
            
            <div class="chat-body-v2">
                @forelse($messages as $message)
                    @php
                        $isMe = (auth()->id() === $message->user_id);
                        $userRole = $message->user->role ?? 'user';
                    @endphp
                    
                    <div class="bubble-v2 {{ $isMe ? 'bubble-me-v2' : 'bubble-other-v2' }} bubble-role-{{ $userRole }}" id="msg-{{ $message->id }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="bubble-name-v2">{{ $isMe ? 'Anda' : $message->user->name }}</span>
                            @if($userRole === 'admin')
                                <span class="role-badge role-badge-admin">Admin</span>
                            @elseif($userRole === 'narasumber')
                                <span class="role-badge role-badge-narasumber">Narasumber</span>
                            @endif
                        </div>

                        {{-- Reply preview --}}
                        @if($message->replyTo)
                            <div class="reply-preview">
                                <div class="reply-preview-name">{{ $message->replyTo->user->name ?? 'User' }}</div>
                                <div class="reply-preview-text">{{ Illuminate\Support\Str::limit($message->replyTo->message, 80) }}</div>
                            </div>
                        @endif

                        <div class="bubble-content-v2">
                            @php
                                $escapedMessage = e($message->message);
                                $linkedMessage = preg_replace(
                                    '/(https?:\/\/[^\s]+)/',
                                    '<a href="$1" target="_blank" rel="noopener noreferrer" class="chat-link">$1</a>',
                                    $escapedMessage
                                );
                            @endphp
                            {!! $linkedMessage !!}
                        </div>
                        
                        <div class="bubble-footer-v2">
                            <span class="bubble-time-v2">{{ $message->created_at->format('H:i') }}</span>
                            
                            <div class="bubble-actions-v2">
                                {{-- Reply button --}}
                                <button type="button" class="btn p-0 border-0 bubble-reply-btn" 
                                    onclick="setReply({{ $message->id }}, '{{ addslashes($message->user->name) }}', '{{ addslashes(Illuminate\Support\Str::limit($message->message, 60)) }}')"
                                    title="Balas">
                                    <i class="bi bi-reply-fill"></i>
                                </button>

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
                    </div>
                @empty
                    <div class="text-center py-5 opacity-50">
                        <p>Belum ada diskusi. Jadilah yang pertama!</p>
                    </div>
                @endforelse
            </div>

            <!-- CHAT FOOTER -->
            @auth
            <form action="{{ route('forum.store') }}" method="POST" id="chat-form">
                @csrf
                <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                <input type="hidden" name="reply_to_id" id="reply-to-id" value="">

                {{-- Reply indicator bar --}}
                <div class="reply-indicator" id="reply-indicator" style="display: none;">
                    <div class="reply-indicator-content">
                        <i class="bi bi-reply-fill me-2"></i>
                        <div>
                            <span class="reply-indicator-name" id="reply-indicator-name"></span>
                            <span class="reply-indicator-text" id="reply-indicator-text"></span>
                        </div>
                    </div>
                    <button type="button" class="reply-indicator-close" onclick="clearReply()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="chat-footer-v2">
                    <input type="text" name="message" class="chat-input-v2" placeholder="Write message" required>

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

        function setReply(messageId, userName, messagePreview) {
            document.getElementById('reply-to-id').value = messageId;
            document.getElementById('reply-indicator-name').textContent = userName;
            document.getElementById('reply-indicator-text').textContent = messagePreview;
            document.getElementById('reply-indicator').style.display = 'flex';
            
            // Focus on input
            document.querySelector('.chat-input-v2').focus();
            
            // Scroll to the message being replied to briefly
            const targetMsg = document.getElementById('msg-' + messageId);
            if (targetMsg) {
                targetMsg.classList.add('bubble-highlight');
                setTimeout(() => targetMsg.classList.remove('bubble-highlight'), 1500);
            }
        }

        function clearReply() {
            document.getElementById('reply-to-id').value = '';
            document.getElementById('reply-indicator').style.display = 'none';
        }
    </script>
</body>
</html>
