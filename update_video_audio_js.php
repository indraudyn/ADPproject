<?php
$files = [
    'resources/views/video/create.blade.php',
    'resources/views/audio/create.blade.php',
    'resources/views/admin/video/create.blade.php',
    'resources/views/admin/audio/create.blade.php',
    'resources/views/narasumber/video/create.blade.php',
    'resources/views/narasumber/audio/create.blade.php',
];

$htmlOld = <<<'HTML'
                        <div class="mb-4" id="sectionWrapper" style="display: none;">
                            <label class="form-label fw-bold">Pilih Bab / Section</label>
                            <select name="section" id="sectionSelect" class="form-select">
                                <option value="" selected>-- Tanpa Bab (Tampil di Detail Parwa) --</option>
                            </select>
                            <div id="sectionLoading" class="text-muted small mt-1" style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Memuat daftar bab...
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Versi (Opsional)</label>
                            <select name="version" class="form-select">
                                <option value="" selected>-- Tanpa Versi --</option>
                                @foreach($versions as $ver)
                                    <option value="{{ $ver }}">{{ $ver }}</option>
                                @endforeach
                            </select>
                        </div>
HTML;

$htmlNew = <<<'HTML'
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Versi (Opsional)</label>
                            <select name="version" id="versionSelect" class="form-select">
                                <option value="" selected>-- Tanpa Versi --</option>
                                @foreach($versions as $ver)
                                    <option value="{{ $ver }}">{{ $ver }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="sectionWrapper" style="display: none;">
                            <label class="form-label fw-bold">Pilih Bab / Section</label>
                            <select name="section" id="sectionSelect" class="form-select">
                                <option value="" selected>-- Tanpa Bab (Tampil di Detail Parwa) --</option>
                            </select>
                            <div id="sectionLoading" class="text-muted small mt-1" style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Memuat daftar bab...
                            </div>
                        </div>
HTML;

$jsNew = <<<'JS'
<script>
document.addEventListener("DOMContentLoaded", function() {
    const parwaSelect = document.querySelector('select[name="parwa_id"]');
    const versionSelect = document.getElementById('versionSelect');
    const sectionWrapper = document.getElementById('sectionWrapper');
    const sectionSelect = document.getElementById('sectionSelect');
    const sectionLoading = document.getElementById('sectionLoading');

    function fetchSections() {
        if (!parwaSelect) return;
        const selectedOption = parwaSelect.options[parwaSelect.selectedIndex];
        const bookName = selectedOption ? selectedOption.text : '';
        const versionName = versionSelect ? versionSelect.value : '';

        if (!bookName || parwaSelect.value === '') {
            sectionWrapper.style.display = 'none';
            return;
        }

        sectionWrapper.style.display = 'block';
        sectionSelect.style.display = 'none';
        sectionLoading.style.display = 'block';
        sectionSelect.innerHTML = '<option value="" selected>-- Tanpa Bab (Tampil di Detail Parwa) --</option>';

        fetch(`/api/parwa/sections-by-book?book=${encodeURIComponent(bookName)}&version=${encodeURIComponent(versionName)}`)
            .then(response => response.json())
            .then(res => {
                sectionLoading.style.display = 'none';
                sectionSelect.style.display = 'block';
                
                const sections = res.data || [];
                if (sections.length > 0) {
                    sections.forEach(sec => {
                        const option = document.createElement('option');
                        option.value = sec.section;
                        option.textContent = sec.section + (sec.sub_parva && sec.sub_parva !== '-' ? ` (${sec.sub_parva})` : '');
                        sectionSelect.appendChild(option);
                    });
                }
            })
            .catch(err => {
                console.error("Error fetching sections:", err);
                sectionLoading.style.display = 'none';
                sectionSelect.style.display = 'block';
            });
    }

    if (parwaSelect) parwaSelect.addEventListener('change', fetchSections);
    if (versionSelect) versionSelect.addEventListener('change', fetchSections);
});
</script>
JS;

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Replace HTML (using regex to ignore exact whitespace differences if any)
    $content = preg_replace('/\<div class=\"mb-4\" id=\"sectionWrapper\"[\s\S]*?\<\/select\>\s*\<\/div\>/', $htmlNew, $content);

    // Replace JS
    $content = preg_replace('/\<script\>\s*document\.addEventListener\(\"DOMContentLoaded\", function\(\) \{[\s\S]*?\}\);\s*\<\/script\>/', $jsNew, $content);
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
