<!-- Navigation Loading Overlay (hidden by default, shown on link click) -->
<div class="nav-loader-overlay" id="navLoaderOverlay">
    <div class="nav-loader-center">
        <div class="nav-loader-ring"></div>
        <p class="nav-loader-label">Memuat data...</p>
    </div>
</div>

<style>
    .nav-loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(248, 250, 252, 0.88);
        backdrop-filter: blur(2px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .nav-loader-overlay.show {
        display: flex;
        opacity: 1;
    }
    .nav-loader-center {
        text-align: center;
    }
    .nav-loader-ring {
        width: 44px;
        height: 44px;
        margin: 0 auto 1rem;
        border: 4px solid #e2e8f0;
        border-top-color: #8b1e1e;
        border-radius: 50%;
        animation: nav-lr-spin 0.75s linear infinite;
    }
    @keyframes nav-lr-spin {
        to { transform: rotate(360deg); }
    }
    .nav-loader-label {
        font-family: 'Figtree', 'Segoe UI', sans-serif;
        font-size: 0.95rem;
        color: #64748b;
        font-weight: 500;
        margin: 0;
        letter-spacing: 0.3px;
    }
</style>

<script>
    let navLoaderTimeout;

    document.addEventListener('click', function(e) {
        var link = e.target.closest('a[href]');
        if (!link) return;

        var href = link.getAttribute('href');
        // Skip non-navigation links
        if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
        // Skip links opening in new tab
        if (link.target === '_blank') return;
        // Skip if modifier keys (ctrl/cmd+click = new tab)
        if (e.ctrlKey || e.metaKey || e.shiftKey) return;
        // Skip download links
        if (link.hasAttribute('download')) return;

        // Delay showing overlay to avoid flashing on fast cache hits
        clearTimeout(navLoaderTimeout);
        navLoaderTimeout = setTimeout(function() {
            var overlay = document.getElementById('navLoaderOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.offsetHeight; // Force reflow
                overlay.classList.add('show');
            }
        }, 300); // 300ms delay
    });

    // Also show on form submit
    document.addEventListener('submit', function(e) {
        var form = e.target;
        // Only for GET forms (page navigation), skip POST forms like logout/delete
        if (form.method && form.method.toUpperCase() !== 'GET') return;
        
        clearTimeout(navLoaderTimeout);
        navLoaderTimeout = setTimeout(function() {
            var overlay = document.getElementById('navLoaderOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.offsetHeight;
                overlay.classList.add('show');
            }
        }, 300);
    });

    // Hide if user navigates back (bfcache)
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            var overlay = document.getElementById('navLoaderOverlay');
            if (overlay) {
                overlay.classList.remove('show');
                overlay.style.display = 'none';
            }
        }
    });
</script>
