/**
 * Back to Top Component JavaScript
 *
 * Minimal JavaScript for back-to-top functionality only
 * Everything else is handled by Preline UI natively
 */

/**
 * Initialize Back to Top functionality
 */
function initializeBackToTop() {
    const backTopButtons = document.querySelectorAll('[id^="back-top-"]');

    backTopButtons.forEach(function(button) {
        const threshold = parseInt(button.getAttribute('data-back-top-threshold')) || 300;
        const autoHide = button.getAttribute('data-back-top-auto-hide') === 'true';
        const progressCircle = button.querySelector('[id$="-progress"]');

        // Show/hide based on scroll position
        function updateButtonVisibility() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercentage = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;

            // Update visibility
            if (autoHide) {
                if (scrollTop > threshold) {
                    button.style.display = 'flex';
                    button.style.opacity = '1';
                } else {
                    button.style.opacity = '0';
                    setTimeout(() => {
                        if (parseFloat(button.style.opacity) === 0) {
                            button.style.display = 'none';
                        }
                    }, 300);
                }
            }

            // Update progress circle (animation handled by CSS)
            if (progressCircle) {
                const circumference = 2 * Math.PI * parseFloat(progressCircle.getAttribute('r'));
                const offset = circumference - (scrollPercentage / 100) * circumference;
                progressCircle.style.strokeDashoffset = offset;
            }
        }

        // Initial update
        updateButtonVisibility();

        // Listen to scroll events with throttling
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    updateButtonVisibility();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Enhanced hover effects sont maintenant dans scroll.css via .back-top-button
    });
}

/**
 * Initialize scroll components on page load
 */
function initializeScrollComponents() {
    // Add smooth scroll class to html if not present
    if (!document.documentElement.classList.contains('scroll-smooth')) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }

    // Initialize back to top buttons
    initializeBackToTop();

    // Handle auto-trigger scroll-to elements
    const autoElements = document.querySelectorAll('[data-scroll-to-trigger="auto"]');
    autoElements.forEach(element => {
        // Auto-scroll after a delay or based on some condition
        setTimeout(() => {
            scrollToTarget(element);
        }, 1000);
    });
}

/**
 * Keyboard navigation support
 */
function initializeKeyboardNavigation() {
    document.addEventListener('keydown', function(event) {
        // Handle Ctrl/Cmd + Home for quick scroll to top
        if ((event.ctrlKey || event.metaKey) && event.key === 'Home') {
            event.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Handle Page Up/Down with smooth scroll
        if (event.key === 'PageUp' || event.key === 'PageDown') {
            event.preventDefault();
            const scrollAmount = window.innerHeight * 0.8;
            const currentScroll = window.pageYOffset;
            const targetScroll = event.key === 'PageUp'
                ? Math.max(0, currentScroll - scrollAmount)
                : currentScroll + scrollAmount;

            window.scrollTo({
                top: targetScroll,
                behavior: 'smooth'
            });
        }
    });
}

/**
 * Utility function to scroll to element by selector
 * Can be used programmatically from other scripts
 */
window.scrollToElement = function(selector, options = {}) {
    const element = document.querySelector(selector);
    if (!element) {
        console.warn(`Element not found: ${selector}`);
        return;
    }

    const defaultOptions = {
        behavior: 'smooth',
        block: 'start',
        offset: 0
    };

    const config = { ...defaultOptions, ...options };

    if (config.offset > 0) {
        element.style.scrollMarginTop = config.offset + 'px';
    }

    element.scrollIntoView({
        behavior: config.behavior,
        block: config.block,
        inline: 'nearest'
    });
};

/**
 * Utility function to get scroll progress percentage
 */
window.getScrollProgress = function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
    return scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
};

/**
 * Initialize everything when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeScrollComponents();
    initializeKeyboardNavigation();
});

/**
 * Re-initialize on dynamic content changes (for HTMX, Livewire, etc.)
 */
document.addEventListener('htmx:afterSwap', function() {
    initializeScrollComponents();
});

document.addEventListener('livewire:navigated', function() {
    initializeScrollComponents();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        scrollToTarget,
        scrollToElement,
        getScrollProgress,
        initializeScrollComponents,
        initializeBackToTop
    };
}