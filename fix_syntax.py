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

for filepath in files:
    if not os.path.exists(filepath):
        continue
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check if the block ends with just `}` instead of `});`
    # Look for the last `</script>` tag that encloses `DOMContentLoaded`
    
    # The problem is that the DOMContentLoaded listener needs `});` at the end.
    if 'document.addEventListener("DOMContentLoaded"' in content:
        # Check if `});` exists before `</script>`
        # This is a bit tricky, let's just do a manual string replacement for the specific faulty block
        
        faulty_block = """    if (versionSelect) {
        versionSelect.removeEventListener('change', fetchSections);
        versionSelect.addEventListener('change', fetchSections);
    }

    </script>"""
        
        fixed_block = """    if (versionSelect) {
        versionSelect.removeEventListener('change', fetchSections);
        versionSelect.addEventListener('change', fetchSections);
    }
});
    </script>"""
        
        if faulty_block in content:
            new_content = content.replace(faulty_block, fixed_block)
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Fixed missing }}); in {filepath}")
        else:
            print(f"No faulty block found in {filepath}")
