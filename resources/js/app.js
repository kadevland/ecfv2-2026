import './bootstrap';
import './components/scrollto';
import './components/easymde';
import './components/map';


// Initialize Preline components on page load
document.addEventListener('DOMContentLoaded', function () {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});

// Reinitialize Preline components for dynamic content (HTMX compatibility)
document.addEventListener('htmx:afterSwap', function() {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});

// Also handle Alpine.js re-initialization if needed
document.addEventListener('alpine:initialized', function() {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});
