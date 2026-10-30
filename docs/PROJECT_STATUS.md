# Estado del proyecto

Fecha de corte: 30 de octubre de 2026.

## Entregado

- Repositorio inicial y rama `main`.
- Sistema visual y tokens documentados.
- Portada responsive reposicionada como marketplace + SaaS + servicios.
- Tres landings navegables.
- Landing premium `/vende-con-nosotros/` con beneficios, planes, proceso, FAQ y formulario.
- Tema de bloques `tiendarosso` y plugin `tiendarosso-commerce` v0.3.0.
- Planes editables sin precios ficticios, solicitudes privadas y estados comerciales.
- WhatsApp configurable por contexto; permanece deshabilitado si no existe número aprobado.
- Roles mínimos, nonce, honeypot, rate limit, auditoría acotada y consentimientos separados.
- Evaluación documentada de Dokan, WCFM, WC Vendors y Product Vendors.
- Infraestructura local validada con WordPress 7.1.4, WooCommerce 11.2.1 y MariaDB 11.4.
- Sprint S3: catálogo local, producto simple/variable/agotado, carrito, compra directa, checkout invitado y pedido WooCommerce real no pagado.
- Moneda BOB, total autoritativo en servidor, confirmación de estado y arquitectura documentada para QR S4.
- Eventos internos de comercio sin píxeles: `view_item`, `add_to_cart`, `remove_from_cart`, `view_cart`, `begin_checkout`, `add_shipping_info` y `add_payment_info`.
- Pruebas Playwright, PHP lint y capturas en `docs/screenshots/`.

## Limitaciones conscientes

- Mockups conceptuales; no son capturas verificadas de los productos.
- S2 todavía no aporta un catálogo comercial aprobado: faltan productos, imágenes, precios, stock, categorías y reglas de envío reales. S3 usa fixtures locales claramente ficticios.
- La portada y las tres landings SaaS siguen pendientes de migración completa a Gutenberg.
- El panel implementa seguimiento básico de solicitudes; no es un CRM externo.
- No hay precios aprobados; los planes usan contacto comercial.
- WooCommerce y pedidos están probados localmente, pero no hay pasarela QR, pagos reales, suscripciones ni activaciones habilitadas.
- No hay motor multivendedor activo ni permisos de comerciante sobre productos o pedidos.
- No hay analítica ni píxeles cargados.
- No hay automatizaciones de IA ni campañas publicitarias activas.
- No hay testimonios, cifras ni casos de éxito inventados.
- No hay despliegue a VPS ni a producción.

## Verificación

Verificación final del 30 de octubre de 2026:

- `npm.cmd audit`: 0 vulnerabilidades conocidas.
- `npm.cmd run build`: compilación correcta con Vite 7.3.7.
- Recursos propios compilados: CSS 11.02 kB gzip y JavaScript 3.05 kB gzip.
- `npm.cmd run test:e2e`: 29/29 pruebas aprobadas en Chromium.
- `npm.cmd run test:wordpress`: 5 pruebas aprobadas y 1 omitida por falta de credenciales administrativas efímeras; checkout, landing, formulario y accesibilidad aprobados.
- Verificador WP-CLI: pedido `#35`, `on-hold`, BOB, Bs.120, SKU `TR-S3-SIMPLE`, sin fecha de pago ni `_tr_purchase_event_id`.
- Interacción verificada: selector, categorías, navegación, cinco rutas, consentimientos y demostraciones sin envío externo.
- WordPress verificado: persistencia privada, catálogo, carrito, checkout y pedido. La comprobación administrativa Gutenberg quedó omitida en esta corrida por no recibir credenciales.
- PHP 8.3 lint: sin errores en el tema y plugin propios.
- Accesibilidad axe: 0 violaciones serias o críticas en las cinco rutas estáticas, la landing WordPress y el checkout.
- Desbordamiento horizontal: aprobado a 1440, 1024, 768 y 390 px.
- Lighthouse portada: rendimiento 96, accesibilidad 100, buenas prácticas 100 y SEO 100; FCP/LCP 2.1 s, TBT 0 ms y CLS 0.
- Lighthouse `/vende-con-nosotros/`: rendimiento 99, accesibilidad 100, buenas prácticas 100 y SEO 100; FCP/LCP 1.2 s, TBT 40 ms y CLS 0.

Las capturas S3 en `docs/screenshots/` cubren carrito de escritorio, checkout móvil y confirmación pendiente en escritorio/móvil. Constituyen evidencia visual local, no aprobación comercial ni pago real.

