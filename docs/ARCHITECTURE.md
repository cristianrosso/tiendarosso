# Arquitectura

## P0 + P1

El prototipo usa HTML semántico, CSS y JavaScript sin framework de interfaz. Vite sirve y compila archivos estáticos; Playwright prueba navegación, interacción, formularios y vistas responsive.

```text
prototype/
  index.html
  assets/
    styles.css
    app.js
  soluciones/
    docentech/index.html
    violet-flow/index.html
    rguilex/index.html
tests/e2e/
infra/
wordpress/
```

## Destino WordPress P2

- Tema de bloques `tiendarosso`: presentación, tokens de `theme.json`, templates, parts y patrones.
- Plugin `tiendarosso-commerce`: soluciones, planes, formularios, oportunidades, consentimiento, eventos e integraciones.
- Contenido comercial administrable desde WordPress; ningún texto clave quedará encerrado en una SPA externa.

## Datos futuros

Los formularios enviarán solicitudes autenticadas con nonce, validación de servidor, rate limiting y protección antispam. Los registros de oportunidad conservarán consentimiento, origen y UTM. La analítica se emitirá desde una capa de datos única para evitar duplicados.

## Infraestructura prevista

Los ejemplos de `infra/` aíslan MariaDB en una red privada y usan volúmenes persistentes. La producción requerirá una red Caddy existente, backups, límites de recursos, secretos fuera de Git y verificación del VPS. P1 no ejecuta ni despliega esos archivos.

