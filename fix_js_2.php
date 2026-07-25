<?php
$files = [
    'resources/views/video/create.blade.php',
    'resources/views/audio/create.blade.php',
    'resources/views/admin/video/create.blade.php',
    'resources/views/admin/audio/create.blade.php',
    'resources/views/narasumber/video/create.blade.php',
    'resources/views/narasumber/audio/create.blade.php',
];

$replacement = <<<JS
    const parwaSelect = document.querySelector('select[name="parwa_id"]');
    const versionSelect = document.getElementById('versionSelect') || document.querySelector('select[name="version"]');
    const sectionWrapper = document.getElementById('sectionWrapper');
    const sectionSelect = document.getElementById('sectionSelect');
    const sectionLoading = document.getElementById('sectionLoading');

    function fetchSections() {
        if (!parwaSelect) return;
        const selectedOption = parwaSelect.options[parwaSelect.selectedIndex];
        const bookName = selectedOption ? selectedOption.text : '';
        const versionName = versionSelect ? versionSelect.value : '';

        if (!bookName || parwaSelect.value === '') {
            if (sectionWrapper) sectionWrapper.style.display = 'none';
            return;
        }

        if (sectionWrapper) sectionWrapper.style.display = 'block';
        if (sectionSelect) sectionSelect.style.display = 'none';
        if (sectionLoading) sectionLoading.style.display = 'block';
        if (sectionSelect) sectionSelect.innerHTML = '<option value="" selected>-- Tanpa Bab (Tampil di Detail Parwa) --</option>';

        fetch(`/ajax/parwa/sections-by-book?book=\${encodeURIComponent(bookName)}&version=\${encodeURIComponent(versionName)}`)
            .then(response => response.json())
            .then(res => {
                if (sectionLoading) sectionLoading.style.display = 'none';
                if (sectionSelect) sectionSelect.style.display = 'block';
                
                const sections = res.data || [];
                if (sections.length > 0) {
                    sections.forEach(sec => {
                        const option = document.createElement('option');
                        option.value = sec.section;
                        option.textContent = sec.section + (sec.sub_parva && sec.sub_parva !== '-' ? ` (\${sec.sub_parva})` : '');
                        if (sectionSelect) sectionSelect.appendChild(option);
                    });
                }
            })
            .catch(err => {
                console.error("Error fetching sections:", err);
                if (sectionLoading) sectionLoading.style.display = 'none';
                if (sectionSelect) sectionSelect.style.display = 'block';
            });
    }

    if (parwaSelect) {
        parwaSelect.removeEventListener('change', fetchSections);
        parwaSelect.addEventListener('change', fetchSections);
    }
    if (versionSelect) {
        versionSelect.removeEventListener('change', fetchSections);
        versionSelect.addEventListener('change', fetchSections);
    }
});
JS;

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Attempt to match the block starting from const parwaSelect up to the closing }); before </script>
    if (preg_match('/const parwaSelect = document\.querySelector[^\n]+\n(?:.*?)(?=\s*<\/script>|\s*if \(sectionSelect\))/s', $content, $matches)) {
        $old_block = $matches[0];
        
        // Remove old block and insert the new replacement
        $content = str_replace($old_block, $replacement . "\n\n", $content);
        
        // Ensure NO duplicate event listener code exists
        $content = preg_replace('/if \(versionSelect\)[^;]+;/', '', $content);
        $content = preg_replace('/if \(parwaSelect\)[^;]+;/', '', $content);
        
        // Clean up formatting
        $content = str_replace("});\n\n});", "});", $content);
        $content = preg_replace("/\}\);\s*\n\s*\}\);/", "});", $content);
        
        file_put_contents($file, $content);
        echo "Updated JS in $file\n";
    }
}
