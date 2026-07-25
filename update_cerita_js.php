<?php
$file = 'resources/views/cerita/create.blade.php';
$content = file_get_contents($file);

$htmlReplacement = <<<'HTML'
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilih Parwa</label>
                                <select name="parwa_id" id="parwaSelect" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Parwa --</option>
                                    @foreach($parwas as $parwa)
                                        <option value="{{ $parwa->name }}" {{ old('parwa_id') == $parwa->name ? 'selected' : '' }}>
                                            {{ $parwa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilih Tipe Versi</label>
                                <select name="versi_tipe" id="versiTipeSelect" class="form-select" required>
                                    <option value="existing" {{ old('versi_tipe', 'existing') == 'existing' ? 'selected' : '' }}>Gunakan Versi Terjemahan yang Ada</option>
                                    <option value="new" {{ old('versi_tipe') == 'new' ? 'selected' : '' }}>Buat Versi Terjemahan Baru</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" id="versiExistingGroup" style="{{ old('versi_tipe', 'existing') == 'existing' ? '' : 'display: none;' }}">
                                <label class="form-label">Pilih Versi Terjemahan</label>
                                <select name="versi_existing" id="versiExistingSelect" class="form-select">
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
HTML;

$jsReplacement = <<<'JS'
    function fetchChapters() {
        const parwaName = document.getElementById('parwaSelect').value;
        const versiTipe = document.getElementById('versiTipeSelect').value;
        const versiExisting = document.getElementById('versiExistingSelect').value;
        const sectionSelect = document.getElementById('sectionSelect');
        const subParwaInput = document.getElementById('subParwaInput');
        
        // Reset selections
        sectionSelect.innerHTML = '<option value="" selected disabled>Loading Bab...</option>';
        subParwaInput.value = '';

        if (!parwaName) {
            sectionSelect.innerHTML = '<option value="" selected disabled>-- Pilih Bab --</option><option value="custom_input">+ Masukkan Bab Baru (Manual) ...</option>';
            return;
        }

        if (versiTipe === 'new') {
            sectionSelect.innerHTML = '<option value="" selected disabled>-- Pilih Bab --</option><option value="custom_input">+ Masukkan Bab Baru (Manual) ...</option>';
            return;
        }

        fetch(`/api/parwa/sections-by-book?book=${encodeURIComponent(parwaName)}&version=${encodeURIComponent(versiExisting)}`)
            .then(res => res.json())
            .then(json => {
                const sectionsList = json.data || [];
                sectionSelect.innerHTML = '<option value="" selected disabled>-- Pilih Bab --</option>';
                
                window.currentSectionsMap = sectionsList;

                sectionsList.forEach(sec => {
                    const option = document.createElement('option');
                    option.value = sec.section;
                    option.textContent = sec.section;
                    sectionSelect.appendChild(option);
                });

                const customOption = document.createElement('option');
                customOption.value = 'custom_input';
                customOption.textContent = '+ Masukkan Bab Baru (Manual) ...';
                sectionSelect.appendChild(customOption);
            })
            .catch(err => {
                console.error('Error fetching sections:', err);
                sectionSelect.innerHTML = '<option value="" selected disabled>-- Pilih Bab --</option><option value="custom_input">+ Masukkan Bab Baru (Manual) ...</option>';
            });
    }

    document.getElementById('parwaSelect').addEventListener('change', fetchChapters);
    document.getElementById('versiTipeSelect').addEventListener('change', fetchChapters);
    document.getElementById('versiExistingSelect').addEventListener('change', fetchChapters);
JS;

// Replace HTML
$content = preg_replace('/\<div class=\"row\"\>[\s\S]*?\<label class=\"form-label\"\>Pilih Parwa\<\/label\>[\s\S]*?\<\/div\>\s*\<\/div\>/', $htmlReplacement, $content);

// Replace JS
$content = preg_replace('/document\.getElementById\(\'parwaSelect\'\)\.addEventListener\(\'change\', function\(\) \{[\s\S]*?\}\);/', $jsReplacement, $content);

file_put_contents($file, $content);
echo "Updated $file\n";
