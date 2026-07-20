// Import EasyMDE for Markdown editor
import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';

// Make EasyMDE available globally
window.EasyMDE = EasyMDE;

// Auto-initialize all EasyMDE textareas
document.addEventListener('DOMContentLoaded', function() {
    initializeEasyMDE();
});

// Also initialize on HTMX content swap
document.addEventListener('htmx:afterSwap', function() {
    initializeEasyMDE();
});

function initializeEasyMDE() {
    document.querySelectorAll('.easymde').forEach(function(textarea) {
        // Skip if already initialized
        if (textarea.nextElementSibling && textarea.nextElementSibling.classList.contains('EasyMDEContainer')) {
            return;
        }

        // Get config from data attributes
        const config = {
            element: textarea,
            placeholder: textarea.dataset.placeholder || 'Écrivez votre contenu en Markdown...',
            spellChecker: false,
            autosave: textarea.dataset.autosave === 'true' ? {
                enabled: true,
                uniqueId: textarea.id || 'markdown-editor-' + Math.random().toString(36).substr(2, 9),
                delay: 1000,
            } : false,
            status: ['lines', 'words', 'cursor'],
            minHeight: textarea.dataset.minHeight || '300px',
            renderingConfig: {
                singleLineBreaks: true,
                codeSyntaxHighlighting: true,
            },
            toolbar: [
                'bold', 'italic', 'heading-1', 'heading-2', 'heading-3', '|',
                'unordered-list', 'ordered-list', 'table', 'quote', '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide', 'undo', 'redo'
            ],
            shortcuts: {
                "toggleBold": "Cmd-B",
                "toggleItalic": "Cmd-I",
                "toggleHeading1": "Cmd-1",
                "toggleHeading2": "Cmd-2",
                "toggleHeading3": "Cmd-3",
                "toggleUnorderedList": "Cmd-L",
                "toggleOrderedList": "Cmd-Alt-L",
                "togglePreview": "Cmd-P",
                "toggleSideBySide": "F9",
                "toggleFullScreen": "F11"
            },
            previewImagesInEditor: true,
            promptURLs: true,
        };

        // Initialize EasyMDE
        const editor = new EasyMDE(config);

        // Store editor instance on textarea for potential future access
        textarea.easyMDE = editor;
    });
}
