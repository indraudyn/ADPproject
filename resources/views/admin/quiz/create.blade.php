<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Buat Soal</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-quiz-create.css') }}">
</head>

<body class="admin-quiz-page">

<div id="wrapper">

@include('layouts.sidebar-admin')

<div class="content">

@include('layouts.topbar-user')

<div class="page-wrapper">

{{-- BACK --}}
<a href="{{ route('admin.quiz.index') }}" class="back-link">
<i class="bi bi-arrow-left"></i> Buat Soal
</a>

{{-- CARD --}}
<div class="quiz-create-card">

<form method="POST" action="{{ route('admin.quiz.store') }}">
@csrf

{{-- SOAL --}}
<div class="form-row">
<label>Soal :</label>
<input type="text" name="question" class="form-control" placeholder="Soal" required>
</div>

{{-- OPSI A --}}
<div class="option-row">
<span>Opsi A :</span>
<input type="text" name="option_a" class="form-control" placeholder="Opsi A" required>
<input type="radio" name="correct_option" value="A" required>
</div>

{{-- OPSI B --}}
<div class="option-row">
<span>Opsi B :</span>
<input type="text" name="option_b" class="form-control" placeholder="Opsi B" required>
<input type="radio" name="correct_option" value="B">
</div>

{{-- OPSI C --}}
<div class="option-row">
<span>Opsi C :</span>
<input type="text" name="option_c" class="form-control" placeholder="Opsi C" required>
<input type="radio" name="correct_option" value="C">
</div>

{{-- OPSI D --}}
<div class="option-row">
<span>Opsi D :</span>
<input type="text" name="option_d" class="form-control" placeholder="Opsi D" required>
<input type="radio" name="correct_option" value="D">
</div>

<div class="text-end mt-4">
<button type="submit" class="btn-upload">
Upload <i class="bi bi-upload ms-1"></i>
</button>
</div>

</form>

</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin-quiz-create.js') }}"></script>
</body>
</html>
