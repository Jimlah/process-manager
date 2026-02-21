/**
 * Theme Toggle — Alpine.js component
 *
 * Supports three modes: 'light', 'dark', 'system'
 * Persists choice to localStorage. Falls back to 'dark' on first visit.
 */
window.themeToggle = function () {
    return {
        theme: 'dark',

        init() {
            this.theme = localStorage.getItem('theme') || 'dark';
            this.apply();

            // Watch for OS-level changes when in "system" mode
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (this.theme === 'system') {
                    this.apply();
                }
            });
        },

        set(value) {
            this.theme = value;
            localStorage.setItem('theme', value);
            this.apply();
        },

        apply() {
            const isDark =
                this.theme === 'dark' ||
                (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            document.documentElement.classList.toggle('dark', isDark);
        },
    };
};
