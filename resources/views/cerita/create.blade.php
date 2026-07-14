<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="UTF-8">
    <title>Create Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Quill CSS --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cerita-create.css') }}">
</head>
<body>
    <x-loading-screen />

<div class="d-flex">

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE TITLE --}}
        <div class="container-fluid mt-4">
            <div class="d-flex align-items-center mb-4">
                @php
                    $backUrl = route('dashboard'); // default for user
                    if (auth()->check()) {
                        if (auth()->user()->role === 'admin') {
                            $backUrl = route('admin.cerita.index');
                        } elseif (auth()->user()->role === 'narasumber') {
                            $backUrl = route('narasumber.dashboard');
                        }
                    }
                @endphp
                <a href="{{ $backUrl }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="mb-0">Upload Cerita</h3>
            </div>

            {{-- FORM CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    {{-- ERROR ALERTS --}}
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-4 shadow-sm">
                            <h6 class="fw-bold mb-2">Gagal Mengupload Cerita:</h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cerita.store') }}" method="POST" id="ceritaForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilih Parwa</label>
                                <select name="parwa_id" id="parwaSelect" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Parwa --</option>
                                    @foreach($parwas as $parwa)
                                        <option value="{{ $parwa->book }}" {{ old('parwa_id') == $parwa->book ? 'selected' : '' }}>
                                            {{ $parwa->book }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilih Bab (Section)</label>
                                <select name="section" id="sectionSelect" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Bab --</option>
                                    <option value="custom_input" {{ old('section') == 'custom_input' ? 'selected' : '' }}>+ Masukkan Bab Baru (Manual) ...</option>
                                    @if(old('section') && old('section') != 'custom_input')
                                        <option value="{{ old('section') }}" selected>{{ old('section') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Bab Baru Input (Hidden by default) -->
                        <div class="row" id="sectionCustomGroup" style="display: none;">
                            <div class="col-md-6 mb-3">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Masukkan Nama Bab Baru</label>
                                <input type="text" name="section_custom" id="sectionCustomInput" class="form-control" placeholder="Contoh: Bab I, Bab IX, dll." value="{{ old('section_custom') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bagian (Sub-Parwa)</label>
                                <input type="text" name="sub_parwa" id="subParwaInput" class="form-control" placeholder="Contoh: Section I" value="{{ old('sub_parwa') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bahasa Cerita</label>
                                <select name="bahasa" class="form-select" required>
                                    <option value="id" {{ old('bahasa') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="en" {{ old('bahasa') == 'en' ? 'selected' : '' }}>Bahasa Inggris (English)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilih Tipe Versi</label>
                                <select name="versi_tipe" id="versiTipeSelect" class="form-select" required>
                                    <option value="existing" {{ old('versi_tipe', 'existing') == 'existing' ? 'selected' : '' }}>Gunakan Versi Terjemahan yang Ada</option>
                                    <option value="new" {{ old('versi_tipe') == 'new' ? 'selected' : '' }}>Buat Versi Terjemahan Baru</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="versiExistingGroup" style="{{ old('versi_tipe', 'existing') == 'existing' ? '' : 'display: none;' }}">
                                <label class="form-label">Pilih Versi Terjemahan</label>
                                <select name="versi_existing" class="form-select">
                                    <option value="" selected disabled>-- Pilih Versi --</option>
                                    @foreach($versions as $ver)
                                        <option value="{{ $ver }}" {{ old('versi_existing') == $ver ? 'selected' : '' }}>
                                            {{ $ver }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="versiBaruGroup" style="{{ old('versi_tipe') == 'new' ? '' : 'display: none;' }}">
                                <label class="form-label">Nama Versi Baru</label>
                                <input type="text" name="versi_baru" class="form-control" placeholder="Contoh: Terjemahan Kadek, Versi Balinese, dll." value="{{ old('versi_baru') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sumber</label>
                                <input type="text" name="sumber" class="form-control" placeholder="Contoh: Sacred-Texts" value="{{ old('sumber') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul Upload Cerita (Detail Info)</label>
                                <input type="text" name="judul" class="form-control" placeholder="Contoh: Kisah Kelahiran Bhisma" value="{{ old('judul') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cerita</label>

                            {{-- QUILL EDITOR --}}
                            <div id="editor" style="height: 300px;">{!! old('cerita') !!}</div>

                            {{-- HIDDEN INPUT --}}
                            <input type="hidden" name="cerita" id="ceritaInput">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-upload">
                                Upload <i class="bi bi-upload ms-1"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Quill JS --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

{{-- INIT QUILL --}}
<script>
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Masukkan cerita...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });

    // Kirim HTML ke backend
    document.getElementById('ceritaForm').addEventListener('submit', function () {
        document.getElementById('ceritaInput').value = quill.root.innerHTML;
    });

    // Dynamic Section (Bab) Fetching
    document.getElementById('parwaSelect').addEventListener('change', function() {
        const bookName = this.value;
        const sectionSelect = document.getElementById('sectionSelect');
        const subParwaInput = document.getElementById('subParwaInput');
        
        // Reset selections
        sectionSelect.innerHTML = '<option value="" selected disabled>Loading Bab...</option>';
        subParwaInput.value = '';

        fetch(`/api/parwa/sections-by-book?book=${encodeURIComponent(bookName)}`)
            .then(res => res.json())
            .then(json => {
                const sectionsList = json.data || [];
                sectionSelect.innerHTML = '<option value="" selected disabled>-- Pilih Bab --</option>';
                
                // Keep track of sections to update sub_parwa
                window.currentSectionsMap = sectionsList;

                sectionsList.forEach(sec => {
                    const option = document.createElement('option');
                    option.value = sec.section;
                    option.textContent = sec.section;
                    sectionSelect.appendChild(option);
                });

                // ALWAYS append the custom input option at the end!
                const customOption = document.createElement('option');
                customOption.value = 'custom_input';
                customOption.textContent = '+ Masukkan Bab Baru (Manual) ...';
                sectionSelect.appendChild(customOption);
            })
            .catch(err => {
                console.error('Error fetching sections:', err);
                sectionSelect.innerHTML = '<option value="" selected disabled>Gagal memuat Bab</option>';
            });
    });

    // Update Sub-Parwa automatically when Bab is selected, and handle custom bab input toggle
    document.getElementById('sectionSelect').addEventListener('change', function() {
        const selectedSection = this.value;
        const subParwaInput = document.getElementById('subParwaInput');
        const customGroup = document.getElementById('sectionCustomGroup');
        const customInput = document.getElementById('sectionCustomInput');

        if (selectedSection === 'custom_input') {
            customGroup.style.display = 'flex';
            customInput.setAttribute('required', 'required');
        } else {
            customGroup.style.display = 'none';
            customInput.removeAttribute('required');
        }

        if (window.currentSectionsMap) {
            const matched = window.currentSectionsMap.find(sec => sec.section === selectedSection);
            if (matched && matched.sub_parva) {
                subParwaInput.value = matched.sub_parva;
            }
        }
    });

    // Handle Version Type Toggle
    document.getElementById('versiTipeSelect').addEventListener('change', function() {
        const type = this.value;
        const existingGroup = document.getElementById('versiExistingGroup');
        const baruGroup = document.getElementById('versiBaruGroup');

        if (type === 'existing') {
            existingGroup.style.display = 'block';
            baruGroup.style.display = 'none';
            existingGroup.querySelector('select').setAttribute('required', 'required');
            baruGroup.querySelector('input').removeAttribute('required');
        } else {
            existingGroup.style.display = 'none';
            baruGroup.style.display = 'block';
            existingGroup.querySelector('select').removeAttribute('required');
            baruGroup.querySelector('input').setAttribute('required', 'required');
        }
    });

    // Run toggle on load to enforce required attributes
    document.getElementById('versiTipeSelect').dispatchEvent(new Event('change'));
</script>

{{-- ALERT ERROR --}}
@if ($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengupload',
            html: `
                <ul class="text-start mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#8b0000',
            customClass: {
                popup: 'rounded-4'
            }
        });
    </script>
@endif

@if (session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengupload',
            text: '{{ session('error') }}',
            confirmButtonColor: '#8b0000',
            customClass: {
                popup: 'rounded-4'
            }
        });
    </script>
@endif

</body>
</html>
