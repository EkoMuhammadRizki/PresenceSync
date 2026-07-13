<?php
    $iconClass = isset($params['toggle-btn-icon-class']) ? $params['toggle-btn-icon-class'] : 'fs-2';
    $isDark = theme()->getCurrentMode() === 'dark';
?>

<!--begin::Theme toggle-->
<a href="#" class="{{ $btnClass }} theme-toggle-btn"
   title="{{ $isDark ? 'Light Mode' : 'Dark Mode' }}">
    <i class="fonticon-{{ $isDark ? 'moon' : 'sun' }} {{ $iconClass }} theme-toggle-icon"></i>
</a>
<!--end::Theme toggle-->

<style>
    .theme-toggle-icon {
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .theme-toggle-icon.spin {
        transform: rotate(360deg);
    }
</style>

@once
<script>
    function animateThemeSwitch(mode) {
        // Spin icon
        var icon = document.querySelector('.theme-toggle-icon');
        if (icon) {
            icon.classList.add('spin');
            setTimeout(function() { icon.classList.remove('spin'); }, 400);
        }

        // Brief page pulse animation
        var overlay = document.createElement('div');
        overlay.className = 'theme-switch-overlay';
        document.body.appendChild(overlay);
        requestAnimationFrame(function() {
            overlay.classList.add('active');
            setTimeout(function() {
                overlay.classList.remove('active');
                setTimeout(function() { overlay.remove(); }, 300);
            }, 300);
        });
    }

    var themeToggleBtn = document.querySelector('.theme-toggle-btn');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var isDark = document.body.classList.contains('dark-mode');
            var mode = isDark ? 'light' : 'dark';

            animateThemeSwitch(mode);

            if (typeof KTApp !== 'undefined' && KTApp.setThemeMode) {
                KTApp.setThemeMode(mode, function() {
                    localStorage.setItem('kt-theme-mode', mode);
                    document.cookie = 'kt-theme-mode=' + mode + ';path=/;max-age=31536000';
                    // Update toggle icon
                    var icon = document.querySelector('.theme-toggle-icon');
                    if (icon) {
                        icon.className = icon.className.replace(/fonticon-(sun|moon)/, 'fonticon-' + (mode === 'dark' ? 'moon' : 'sun'));
                    }
                    // Update button title
                    themeToggleBtn.title = mode === 'dark' ? 'Light Mode' : 'Dark Mode';
                    // Dispatch custom event
                    document.dispatchEvent(new CustomEvent('kt-theme-mode-changed', { detail: { mode: mode } }));
                });
            }
        });
    }
</script>
<style>
    .theme-switch-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        pointer-events: none;
        background: radial-gradient(circle at 50% 30%, rgba(var(--bs-primary-rgb, 0,158,247), 0.12), transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .theme-switch-overlay.active {
        opacity: 1;
    }
</style>
@endonce
