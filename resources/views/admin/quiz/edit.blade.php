<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Soal</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-quiz-edit.css') }}">
</head>

<body class="admin-quiz-page">

<div id="wrapper">

@include('layouts.sidebar-admin')

<div class="content">

@include('layouts.topbar-user')

<div class="page-wrapper">

<a href="{{ route('admin.quiz.index') }}" class="back-link">
<i class="bi bi-arrow-left"></i> Edit Soal
</a>

<div class="quiz-edit-card">

<form method="POST"
      action="{{ route('admin.quiz.update',$quiz->id) }}">
@csrf
@method('PUT')

{{-- SOAL --}}
<div class="form-row">
<label>Soal :</label>
<input type="text"
       name="question"
       class="form-control"
       value="{{ $quiz->question }}"
       required>
</div>

{{-- OPSI A --}}
<div class="option-row">
<span>Opsi A :</span>
<input type="text"
       name="option_a"
       class="form-control"
       value="{{ $quiz->option_a }}"
       required>
<input type="radio"
       name="correct_option"
       value="A"
       {{ $quiz->correct_option=='A' ? 'checked':'' }}>
</div>

{{-- OPSI B --}}
<div class="option-row">
<span>Opsi B :</span>
<input type="text"
       name="option_b"
       class="form-control"
       value="{{ $quiz->option_b }}"
       required>
<input type="radio"
       name="correct_option"
       value="B"
       {{ $quiz->correct_option=='B' ? 'checked':'' }}>
</div>

{{-- OPSI C --}}
<div class="option-row">
<span>Opsi C :</span>
<input type="text"
       name="option_c"
       class="form-control"
       value="{{ $quiz->option_c }}"
       required>
<input type="radio"
       name="correct_option"
       value="C"
       {{ $quiz->correct_option=='C' ? 'checked':'' }}>
</div>

{{-- OPSI D --}}
<div class="option-row">
<span>Opsi D :</span>
<input type="text"
       name="option_d"
       class="form-control"
       value="{{ $quiz->option_d }}"
       required>
<input type="radio"
       name="correct_option"
       value="D"
       {{ $quiz->correct_option=='D' ? 'checked':'' }}>
</div>

<div class="text-end mt-4">
<button class="btn-upload">
Update <i class="bi bi-save ms-1"></i>
</button>
</div>

</form>

</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin-quiz-edit.js') }}"></script>
</body>
</html>
