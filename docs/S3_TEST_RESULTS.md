# Resultados Sprint S3

Fecha: 30 de octubre de 2026. Entorno: localhost, WordPress 7.1.4, WooCommerce 11.2.1, MariaDB 11.4 y Chromium Playwright.

## Flujos verificados

- Carrito vacío con retorno a tienda.
- Producto agotado sin acción de compra.
- Producto variable con selección “Mediano”, precio Bs.200 y alta al carrito.
- Producto simple Bs.120, incremento/decremento de cantidad y recálculo por Store API.
- Checkout invitado con validación de campos, envío gratuito local y método no pagado.
- Pedido local `#35`: estado `on-hold`, BOB, total servidor Bs.120, SKU `TR-S3-SIMPLE`, sin fecha de pago ni evento `purchase`.
- Manipulación del texto del total a Bs.1 descartada al crear el pedido: WooCommerce conservó Bs.120.
- Sin desbordamiento horizontal a 1440, 1024, 768 y 390 px.
- Axe sin violaciones serias o críticas en checkout.
- Regresión estática: 29/29 pruebas aprobadas.
- Suite WordPress: 5 aprobadas y 1 omitida por ausencia de credenciales administrativas efímeras.

## Evidencia

- `s3-cart-desktop-1440.png`
- `s3-checkout-mobile-390.png`
- `s3-order-pending-mobile-390.png`
- `s3-order-pending-desktop-1440.png`

Los productos, el método y el pedido son fixtures locales ficticios; no constituyen catálogo, precio ni pago de producción. La pasarela QR real queda pendiente de S4.

## Dependencias y riesgos pendientes

- S2 debe entregar catálogo, imágenes, precios, inventario, categorías y condiciones de envío aprobados antes de producción.
- S4 necesita proveedor QR, credenciales sandbox, especificación de firma/webhook y reglas de expiración/reembolso.
- Producción necesita SMTP transaccional, HTTPS, backups, política de retención y revisión legal/fiscal.
- S6 debe resolver pedidos mixtos, responsabilidad de envío, comisiones, liquidaciones y aislamiento de vendedores.
