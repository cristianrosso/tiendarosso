# Estado del proyecto

Fecha de corte: 29 de octubre de 2026.

## Entregado

- Repositorio inicial y rama `main`.
- Sistema visual y tokens documentados.
- Portada responsive con explorador interactivo.
- Tres landings navegables.
- Navegación móvil, FAQ, CTAs y formulario visual con consentimiento.
- Infraestructura de desarrollo/producción como ejemplo no ejecutado.
- Pruebas Playwright y capturas en `docs/screenshots/`.

## Limitaciones conscientes

- Prototipo estático; no es un tema WordPress ni es editable en Gutenberg.
- Mockups conceptuales; no son capturas verificadas de los productos.
- No hay backend, CRM, almacenamiento ni envío de leads.
- No hay precios aprobados; los planes usan contacto comercial.
- No hay WooCommerce, pagos, suscripciones ni activaciones.
- No hay analítica ni píxeles cargados.
- No hay testimonios, cifras ni casos de éxito inventados.
- No hay despliegue a VPS ni a producción.

## Verificación

Verificación final del 29 de octubre de 2026:

- `npm.cmd audit`: 0 vulnerabilidades conocidas.
- `npm.cmd run build`: compilación correcta con Vite 7.3.7.
- Recursos propios compilados: CSS 8.69 kB gzip y JavaScript 2.79 kB gzip.
- `npm.cmd run test:e2e`: 24/24 pruebas aprobadas en Chromium.
- Interacción verificada: selector, navegación, landings, consentimiento y ausencia de envío externo.
- Accesibilidad axe: 0 violaciones serias o críticas en las cuatro rutas.
- Desbordamiento horizontal: aprobado a 1440, 1024, 768 y 390 px.
- Lighthouse sobre la compilación local de producción: rendimiento 99, accesibilidad 100, buenas prácticas 100 y SEO 100.
- Métricas Lighthouse: FCP 1.7 s, LCP 1.7 s, TBT 0 ms y CLS 0.

Las 10 capturas finales están en `docs/screenshots/`: portada a 1440, 1024, 768 y 390 px; cada landing a 1440 y 390 px. Constituyen evidencia visual, no aprobación comercial del diseño.

