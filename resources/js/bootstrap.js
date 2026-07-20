// Alpine.js + Plugins
import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import intersect from "@alpinejs/intersect";
import collapse from "@alpinejs/collapse";
import anchor from "@alpinejs/anchor";
import "htmx-ext-alpine-morph";

// Register Alpine.js plugins
Alpine.plugin(focus);
Alpine.plugin(intersect);
Alpine.plugin(collapse);
Alpine.plugin(anchor);

window.Alpine = Alpine;
Alpine.start();

// HTMX
import "htmx.org";

//Preline UI JavaScript
import "preline";
import "preline/preline";
import "@preline/remove-element";
import "@preline/accordion";
import "@preline/carousel";
import "@preline/collapse";
import "@preline/dropdown";
import "@preline/overlay";
import "@preline/range-slider";
import "@preline/remove-element";
import "@preline/scroll-nav";
import "@preline/scrollspy";
import "@preline/select";
import "@preline/stepper";
import "@preline/tabs";
import "@preline/tooltip";
