# Checkout y estados de pedido

## Alcance S3

Las rutas `/shop/`, `/cart/` y `/checkout/` usan WooCommerce 11.2.1 y sus bloques oficiales. El tema aporta presentación FSE; el plugin `tiendarosso-commerce` contiene las reglas comerciales adicionales. Los fixtures de catálogo y pago son ficticios, exclusivos de localhost y no se ejecutan automáticamente.

## Estados visibles

| Estado WooCommerce | Significado operativo |
| --- | --- |
| `pending` | Pedido creado, pago todavía no confirmado. |
| `on-hold` | Pedido recibido por un método diferido; no preparar como pagado. |
| `processing` | Pago confirmado; requiere preparación. |
| `completed` | Pedido finalizado. |
| `failed` / `cancelled` | No continuar fulfillment. |
| `refunded` | Reembolso registrado por WooCommerce/pasarela. |

La pantalla de confirmación lee el pedido del servidor y distingue explícitamente pago confirmado de pedido recibido. El método local “Pago de prueba — no realizado” crea `on-hold`; no cobra, no envía fondos y no emite `purchase`.

## Contrato para QR en S4

1. El backend crea la intención con `order_id`, total BOB y clave de idempotencia.
2. El frontend solo presenta el QR y consulta un estado no autoritativo.
3. Un webhook firmado confirma o rechaza el pago.
4. El backend valida firma, importe, moneda, referencia, estado previo e idempotencia.
5. Solo una confirmación válida llama a `payment_complete`; la página de agradecimiento nunca confirma por sí sola.

Se deben probar éxito, expiración, rechazo, webhook duplicado, importe incorrecto, reintento y reembolso. Sin credenciales sandbox y aprobación del proveedor, S4 permanece pendiente.

## Notificaciones y multivendedor

Producción requerirá SMTP/transaccional aprobado y plantillas revisadas; el fixture local desactiva correos de pedidos. Para S6, cada línea podrá conservar `_tr_vendor_id`, pero todavía no existen reparto, comisión, saldo ni acceso del comerciante. Esos permisos se habilitarán solo después de probar aislamiento entre vendedores.
