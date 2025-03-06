import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import tailwindcss from '@tailwindcss/vite';

// https://vite.dev/config/
export default defineConfig({
  plugins: [
      tailwindcss(),
      svelte({
              include: ['src/**/*.ts', 'src/**/*.js', 'src/**/*.svelte'],
              compilerOptions: {
                dev: true
              }
          }
      ),
   ],
})
