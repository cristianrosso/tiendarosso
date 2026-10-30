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

## WordPress P2/P4

- Tema de bloques `tiendarosso`: presentación, tokens de `theme.json`, templates, parts y patrones.
- Plugin `tiendarosso-commerce`: soluciones, planes, formularios, oportunidades, consentimiento, eventos e integraciones.
- Contenido comercial administrable desde WordPress; ningún texto clave quedará encerrado en una SPA externa.

## Implementación WordPress v0.3.0

- `wordpress/wp-content/themes/tiendarosso`: tema FSE con tokens, templates y estilos editoriales.
- `wordpress/wp-content/plugins/tiendarosso-commerce`: planes, solicitudes, configuración, roles y bloques dinámicos.
- `/vende-con-nosotros/`: página creada al activar el plugin y compuesta por bloques editables.
- WooCommerce es dependencia declarada; el motor multivendedor no está acoplado todavía.
- Los templates FSE `page-cart.html` y `page-checkout.html` envuelven los bloques oficiales sin reemplazar la lógica de WooCommerce.
- `TR_Checkout` agrega compra directa con nonce, estado de pedido, eventos acotados y un punto de integración futura para vendedor/pasarela.

La vista en `prototype/` continúa siendo una herramienta de revisión visual. La persistencia, el carrito, los pedidos y el panel administrativo pertenecen a WordPress/WooCommerce.

## Flujo transaccional S3

```text
Producto/variación -> carrito WooCommerce -> Checkout Block -> pedido on-hold
                                                        |
                                                        +-> webhook auténtico futuro -> payment_complete
```

El navegador no decide precio, stock ni estado pagado. `purchase` queda reservado a `woocommerce_payment_complete`; visitar la página de confirmación no lo dispara. La referencia privada `_tr_vendor_id` puede copiarse a la línea del pedido cuando exista un adaptador multivendedor probado.

## Datos

El formulario comercial ya envía solicitudes autenticadas con nonce, validación de servidor, rate limiting y protección antispam. Los registros privados conservan consentimiento, origen y UTM. La analítica futura se emitirá desde una capa de datos única para evitar duplicados.

## Infraestructura prevista

Los ejemplos de `infra/` aíslan MariaDB en una red privada y usan volúmenes persistentes. El entorno de desarrollo se validó localmente; los contenedores se detienen sin borrar volúmenes. La producción requerirá red Caddy, backups, límites de recursos, secretos fuera de Git y verificación del VPS. v0.2.0 no despliega esos archivos.

