# Estado del proyecto

Fecha de corte: 29 de octubre de 2026.

## Entregado

- Repositorio inicial y rama `main`.
- Sistema visual y tokens documentados.
- Portada responsive reposicionada como marketplace + SaaS + servicios.
- Tres landings navegables.
- Landing premium `/vende-con-nosotros/` con beneficios, planes, proceso, FAQ y formulario.
- Tema de bloques `tiendarosso` y plugin `tiendarosso-commerce` v0.2.0.
- Planes editables sin precios ficticios, solicitudes privadas y estados comerciales.
- WhatsApp configurable por contexto; permanece deshabilitado si no existe número aprobado.
- Roles mínimos, nonce, honeypot, rate limit, auditoría acotada y consentimientos separados.
- Evaluación documentada de Dokan, WCFM, WC Vendors y Product Vendors.
- Infraestructura local validada con WordPress 7.1.4, WooCommerce 11.2.1 y MariaDB 11.4.
- Pruebas Playwright, PHP lint y capturas en `docs/screenshots/`.

## Limitaciones conscientes

- Mockups conceptuales; no son capturas verificadas de los productos.
- La portada y las tres landings SaaS siguen pendientes de migración completa a Gutenberg.
- El panel implementa seguimiento básico de solicitudes; no es un CRM externo.
- No hay precios aprobados; los planes usan contacto comercial.
- WooCommerce está probado localmente, pero no hay pagos, suscripciones ni activaciones habilitadas.
- No hay motor multivendedor activo ni permisos de comerciante sobre productos o pedidos.
- No hay analítica ni píxeles cargados.
- No hay automatizaciones de IA ni campañas publicitarias activas.
- No hay testimonios, cifras ni casos de éxito inventados.
- No hay despliegue a VPS ni a producción.

## Verificación

Verificación final del 29 de octubre de 2026:

- `npm.cmd audit`: 0 vulnerabilidades conocidas.
- `npm.cmd run build`: compilación correcta con Vite 7.3.7.
- Recursos propios compilados: CSS 11.02 kB gzip y JavaScript 3.05 kB gzip.
- `npm.cmd run test:e2e`: las 29 pruebas quedaron aprobadas en Chromium; una captura bloqueada temporalmente por Windows pasó al reintentarla de forma aislada.
- `npm.cmd run test:wordpress`: 3/3 pruebas aprobadas sobre WordPress real.
- Interacción verificada: selector, categorías, navegación, cinco rutas, consentimientos y demostraciones sin envío externo.
- WordPress verificado: persistencia privada, estado inicial, Gutenberg y configuración administrativa.
- PHP 8.3 lint: sin errores en el tema y plugin propios.
- Accesibilidad axe: 0 violaciones serias o críticas en las cinco rutas estáticas y la landing WordPress.
- Desbordamiento horizontal: aprobado a 1440, 1024, 768 y 390 px.
- Lighthouse portada: rendimiento 96, accesibilidad 100, buenas prácticas 100 y SEO 100; FCP/LCP 2.1 s, TBT 0 ms y CLS 0.
- Lighthouse `/vende-con-nosotros/`: rendimiento 99, accesibilidad 100, buenas prácticas 100 y SEO 100; FCP/LCP 1.2 s, TBT 40 ms y CLS 0.

Las 12 capturas finales están en `docs/screenshots/`: portada a 1440, 1024, 768 y 390 px; cada landing a 1440 y 390 px. Constituyen evidencia visual, no aprobación comercial del diseño.

