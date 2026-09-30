import { router } from '@inertiajs/vue3';

const STYLE_ID = 'theme-overrides';

export function applyThemeCss(css: string): void {
    let el = document.getElementById(STYLE_ID);

    if (!el) {
        el = document.createElement('style');
        el.id = STYLE_ID;
        document.head.appendChild(el);
    }

    if (el.textContent !== css) {
        el.textContent = css;
    }
}

export function initializeTheme(): void {
    router.on('navigate', (event) => {
        const css = (event as CustomEvent).detail?.page?.props?.themeCss;

        if (typeof css === 'string') {
            applyThemeCss(css);
        }
    });
}
