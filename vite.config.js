import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

const ReactCompilerConfig = { target: '19' };

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.tsx'],
      ssr: 'resources/js/ssr.jsx',
      refresh: true,
      // Force the full refresh approach over HMR for more reliability
      valetTls: false,
    }),
    react({ babel: { plugins: [['babel-plugin-react-compiler', ReactCompilerConfig]] } }),
    tailwindcss(),
  ],
  esbuild: {
    jsx: 'automatic',
  },
  server: {
    host: '127.0.0.1',
    port: 5173,
    strictPort: true,
    hmr: {
      host: '127.0.0.1',
      protocol: 'http',
    },
  },
});
