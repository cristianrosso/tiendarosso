# TIENDA ROSSO

Portal comercial para presentar y vender soluciones tecnológicas, servicios digitales y, en fases futuras, productos de terceros.

## Estado actual

La versión `0.1.0` cubre **P0 + P1**: arquitectura inicial, sistema visual, prototipo navegable de la portada y landings de Docentech, Violet Flow y Rguilex. El prototipo es estático y **todavía no es editable desde Gutenberg**. La conversión a tema de bloques y patrones corresponde a P2.

No hay pagos, suscripciones, activaciones, CRM ni envío real de formularios. Los formularios de P1 son demostraciones de interfaz y no transmiten datos.

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

## Rutas del prototipo

- `/` — portada y explorador interactivo.
- `/soluciones/docentech/`
- `/soluciones/violet-flow/`
- `/soluciones/rguilex/`

## Estructura

- `prototype/`: experiencia P1 estática.
- `tests/e2e/`: pruebas funcionales y capturas con Playwright.
- `docs/`: especificación, arquitectura, diseño, embudos y estado.
- `infra/`: ejemplos de desarrollo y producción sin despliegue.
- `wordpress/`: punto de entrada documentado para P2.

## Seguridad

No se versionan secretos. Usa `.env.example` como referencia. No se ha desplegado a producción ni se han modificado otros repositorios, bases de datos o servicios.

