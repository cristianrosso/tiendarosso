import { defineConfig } from 'vite';
import { resolve } from 'node:path';

export default defineConfig({
  root: 'prototype',
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        home: resolve(import.meta.dirname, 'prototype/index.html'),
        docentech: resolve(import.meta.dirname, 'prototype/soluciones/docentech/index.html'),
        violetFlow: resolve(import.meta.dirname, 'prototype/soluciones/violet-flow/index.html'),
        rguilex: resolve(import.meta.dirname, 'prototype/soluciones/rguilex/index.html'),
        vendeConNosotros: resolve(import.meta.dirname, 'prototype/vende-con-nosotros/index.html')
      }
    }
  }
});

