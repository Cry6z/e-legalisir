const ThemeManager = (() => {
    const storageKey = 'e-legalisir-theme';
    let currentTheme = 'light';
    let transitionTimer;

    const prefersDark = () =>
        window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    const getStoredTheme = () => window.localStorage.getItem(storageKey);

    const startTransition = () => {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const root = document.documentElement;
        root.classList.add('theme-transition');

        clearTimeout(transitionTimer);
        transitionTimer = window.setTimeout(() => {
            root.classList.remove('theme-transition');
        }, 400);
    };

    const applyTheme = (theme) => {
        currentTheme = theme === 'dark' ? 'dark' : 'light';
        const root = document.documentElement;

        startTransition();

        if (currentTheme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }

        window.localStorage.setItem(storageKey, currentTheme);

        queueMicrotask(updateToggles);
    };

    const updateToggles = () => {
        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.setAttribute('aria-pressed', currentTheme === 'dark' ? 'true' : 'false');
            button.dataset.theme = currentTheme;
        });
    };

    const init = () => {
        const stored = getStoredTheme();
        const initialTheme = stored ?? (prefersDark() ? 'dark' : 'light');

        applyTheme(initialTheme);

        if (window.matchMedia) {
            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', (event) => {
                    if (!getStoredTheme()) {
                        applyTheme(event.matches ? 'dark' : 'light');
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', updateToggles);
    };

    const toggle = () => {
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
    };

    return {
        init,
        toggle,
    };
})();

ThemeManager.init();
window.toggleTheme = () => ThemeManager.toggle();
