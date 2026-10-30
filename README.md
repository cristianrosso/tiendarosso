# TIENDA ROSSO

Marketplace y portal comercial para productos de terceros, soluciones tecnológicas propias y servicios de crecimiento para negocios.

## Estado actual

La versión `0.2.0` conserva P0 + P1 y entrega el primer corte funcional de P2/P4: portada orientada a marketplace, landing premium `/vende-con-nosotros/`, tema de bloques, planes editables, formulario persistente, panel comercial y WhatsApp configurable.

WooCommerce se validó solo en el entorno local. Todavía no hay pagos, suscripciones, activaciones, campañas, motor multivendedor ni despliegue de producción. El formulario estático es una demostración; el bloque WordPress sí registra solicitudes privadas.

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
```

## Rutas del prototipo

- `/` — portada y explorador interactivo.
- `/soluciones/docentech/`
- `/soluciones/violet-flow/`
- `/soluciones/rguilex/`
- `/vende-con-nosotros/`

## Estructura

- `prototype/`: experiencia visual estática y revisable.
- `tests/e2e/`: pruebas funcionales y capturas con Playwright.
- `tests/wordpress/`: persistencia, accesibilidad y edición administrativa.
- `docs/`: especificación, arquitectura, diseño, embudos y estado.
- `infra/`: ejemplos de desarrollo y producción sin despliegue.
- `wordpress/`: tema de bloques y plugin comercial v0.2.0.

## Seguridad

No se versionan secretos. Usa `.env.example` como referencia. No se ha desplegado a producción ni se han modificado otros repositorios, bases de datos o servicios. Los volúmenes de desarrollo se preservan y los contenedores pueden detenerse sin borrar datos.

