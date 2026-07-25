import os
import re

files = [
    'resources/views/video/create.blade.php',
    'resources/views/audio/create.blade.php',
    'resources/views/admin/video/create.blade.php',
    'resources/views/admin/audio/create.blade.php',
    'resources/views/narasumber/video/create.blade.php',
    'resources/views/narasumber/audio/create.blade.php',
    'resources/views/video/edit.blade.php',
    'resources/views/audio/edit.blade.php',
    'resources/views/admin/video/edit.blade.php',
    'resources/views/admin/audio/edit.blade.php',
    'resources/views/narasumber/video/edit.blade.php',
    'resources/views/narasumber/audio/edit.blade.php',
]

replacement = """
    const parwaSelect = document.querySelector('select[name="parwa_id"]');
    const versionSelect = document.querySelector('select[name="version"]');
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

        fetch(`/ajax/parwa/sections-by-book?book=${encodeURIComponent(bookName)}&version=${encodeURIComponent(versionName)}`)
            .then(response => response.json())
            .then(res => {
                if (sectionLoading) sectionLoading.style.display = 'none';
                if (sectionSelect) sectionSelect.style.display = 'block';
                
                const sections = res.data || [];
                if (sections.length > 0) {
                    sections.forEach(sec => {
                        const option = document.createElement('option');
                        option.value = sec.section;
                        option.textContent = sec.section + (sec.sub_parva && sec.sub_parva !== '-' ? ` (${sec.sub_parva})` : '');
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
"""

for filepath in files:
    if not os.path.exists(filepath):
        continue
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # We want to match from `const parwaSelect = document.querySelector('select[name="parwa_id"]');`
    # up to just before `if (sectionSelect) { \n sectionSelect.addEventListener('change'` or `</script>`
    
    pattern = re.compile(r'const parwaSelect = document\.querySelector\(\'select\[name="parwa_id"\]\'\);.*?(?=if \(sectionSelect\) \{|</script>)', re.DOTALL)
    
    if pattern.search(content):
        # Apply replacement
        new_content = pattern.sub(replacement + "\n    ", content)
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated JS in {filepath}")
    else:
        print(f"Pattern not found in {filepath}")
