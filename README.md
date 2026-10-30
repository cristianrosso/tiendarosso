# TIENDA ROSSO

Marketplace y portal comercial para productos de terceros, soluciones tecnológicas propias y servicios de crecimiento para negocios.

## Estado actual

La versión `0.3.0` conserva P0 + P1, amplía P2/P4 y completa el Sprint S3 de compra local: catálogo WooCommerce, producto simple/variable/agotado, carrito, checkout invitado, pedido real no pagado y confirmación de estado veraz.

WooCommerce se validó solo en el entorno local. No hay pasarela QR ni pago real, suscripciones, activaciones, campañas, motor multivendedor ni despliegue de producción. El método de cheque de WooCommerce se usa únicamente como instrumento local para crear un pedido `on-hold` y nunca se presenta como pago confirmado.

## Inicio rápido

Requisitos: Node.js 20 o superior.

```powershell
npm.cmd install
npm.cmd run dev
```

La terminal mostrará la URL local. Para compilar y probar:

```powershell
npm.cmd run build
npm.cmd run test:e2e
```

La prueba WordPress requiere una instalación local activa y credenciales efímeras en `WP_ADMIN_USER`, `WP_ADMIN_PASSWORD` y `WP_SELLER_PAGE_ID`:

```powershell
npm.cmd run test:wordpress
npm.cmd run test:checkout
```

## Rutas del prototipo

- `/` — portada y explorador interactivo.
- `/soluciones/docentech/`
- `/soluciones/violet-flow/`
- `/soluciones/rguilex/`
- `/vende-con-nosotros/`
- `/shop/`, `/cart/` y `/checkout/` — WordPress local con WooCommerce activo.

## Estructura

- `prototype/`: experiencia visual estática y revisable.
- `tests/e2e/`: pruebas funcionales y capturas con Playwright.
- `tests/wordpress/`: persistencia, accesibilidad, edición administrativa y checkout real local.
- `tests/fixtures/`: catálogo y verificación de pedido restringidos a localhost.
- `docs/`: especificación, arquitectura, diseño, embudos y estado.
- `infra/`: ejemplos de desarrollo y producción sin despliegue.
- `wordpress/`: tema de bloques y plugin comercial v0.3.0.

## Seguridad

No se versionan secretos. Usa `.env.example` como referencia. No se ha desplegado a producción ni se han modificado otros repositorios, bases de datos o servicios. Los volúmenes de desarrollo se preservan y los contenedores pueden detenerse sin borrar datos.

