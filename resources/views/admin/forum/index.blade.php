<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Forum Diskusi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Dashboard & Forum CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forum-admin.css') }}">
</head>

<body class="forum-page">

<div id="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-admin')

    {{-- CONTENT --}}
    <div class="content">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE --}}
        <div class="page-wrapper">

            <h3 class="page-title text-center mb-4">Forum Diskusi</h3>

            <div class="forum-card">
                
                {{-- CHAT AREA --}}
                <div class="chat-body" id="chatBody">
                    @forelse ($messages as $msg)
                        @php
                            $isMe = $msg->user_id === auth()->id();
                        @endphp

                        <div class="chat-row {{ $isMe ? 'me' : 'other' }}">
                            <div class="chat-bubble {{ $isMe ? 'bubble-me' : 'bubble-other' }}">
                                @unless($isMe)
                                    <div class="chat-username">
                                        {{ $msg->user->name }}
                                    </div>
                                @endunless

                                <div class="chat-text">
                                    {{ $msg->message }}
                                </div>

                                {{-- <div class="chat-time">
                                    {{ $msg->created_at->format('H:i') }}
                                </div> --}}
                            </div>
                        </div>
                    @empty
                        {{-- kosongkan, JANGAN tampilkan tulisan apa pun --}}
                    @endforelse
                </div>

                {{-- INPUT CHAT (TETAP ADA MESKI CHAT KOSONG) --}}
                <form action="{{ route('forum.store') }}" method="POST" class="chat-input">
                    @csrf
                    <input
                        type="text"
                        name="message"
                        class="form-control chat-text-input"
                        placeholder="Write message..."
                        autocomplete="off"
                        required
                    >

                    <button type="submit" class="btn btn-send">
                        Send <i class="bi bi-send ms-1"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/forum-admin.js') }}"></script>
</body>
</html>
