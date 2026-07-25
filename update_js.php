<?php
$files = [
    'resources/views/video/create.blade.php',
    'resources/views/audio/create.blade.php',
    'resources/views/admin/video/create.blade.php',
    'resources/views/admin/audio/create.blade.php',
    'resources/views/narasumber/video/create.blade.php',
    'resources/views/narasumber/audio/create.blade.php',
];

$jsReplacement = <<<'JS'
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
                    
                    const customOption = document.createElement('option');
                    customOption.value = 'custom_input';
                    customOption.textContent = '+ Masukkan Bab Baru (Manual) ...';
                    sectionSelect.appendChild(customOption);
                })
                .catch(err => {
                    console.error("Error fetching sections:", err);
                    sectionLoading.style.display = 'none';
                    sectionSelect.style.display = 'block';
                    
                    const customOption = document.createElement('option');
                    customOption.value = 'custom_input';
                    customOption.textContent = '+ Masukkan Bab Baru (Manual) ...';
                    sectionSelect.appendChild(customOption);
                });
        });
    }

    if (sectionSelect) {
        sectionSelect.addEventListener('change', function() {
            const selectedSection = this.value;
            let sectionCustomGroup = document.getElementById('sectionCustomGroup');
            
            if (!sectionCustomGroup) {
                sectionCustomGroup = document.createElement('div');
                sectionCustomGroup.id = 'sectionCustomGroup';
                sectionCustomGroup.className = 'mt-2';
                sectionCustomGroup.style.display = 'none';
                sectionCustomGroup.innerHTML = '<input type="text" id="sectionCustomInput" class="form-control" placeholder="Masukkan nama bab baru...">';
                sectionSelect.parentNode.insertBefore(sectionCustomGroup, sectionSelect.nextSibling);
            }
            
            const sectionCustomInput = document.getElementById('sectionCustomInput');

            if (selectedSection === 'custom_input') {
                sectionCustomGroup.style.display = 'block';
                sectionCustomInput.setAttribute('required', 'required');
                sectionSelect.removeAttribute('name');
                sectionCustomInput.setAttribute('name', 'section');
            } else {
                sectionCustomGroup.style.display = 'none';
                sectionCustomInput.removeAttribute('required');
                sectionSelect.setAttribute('name', 'section');
                sectionCustomInput.removeAttribute('name');
            }
        });
    }
});
</script>
JS;

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    // Replace everything from ".then(res => {" up to "</script>"
    $content = preg_replace('/\.then\(res => \{.*?\<\/script\>/s', $jsReplacement, $content);
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
