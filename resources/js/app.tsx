import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { route as routeFn } from 'ziggy-js';
import { initializeTheme } from './hooks/use-appearance.js';

declare global {
    const route: typeof routeFn;
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Initialize theme early to prevent flickering
initializeTheme();

interface PageProps {
  el: HTMLElement;
  App: React.ComponentType<any>;
  props: Record<string, unknown>;
}

createInertiaApp({
    title: (title: string) => `${title} - ${appName}`,
    resolve: (name: string) => {
        const pageModules = import.meta.glob<Record<string, any>>('./pages/**/*.tsx', { eager: true });
        return pageModules[`./pages/${name}.tsx`];
    },
    setup({ el, App, props }: PageProps) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
