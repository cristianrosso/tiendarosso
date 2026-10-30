# Arquitectura comercial

## Líneas de negocio

1. **Marketplace:** categorías, negocios, productos físicos/digitales, consultas y futuros pedidos.
2. **SaaS propio:** Docentech, Violet Flow y Rguilex con landings y demostraciones verificables.
3. **Servicios para negocios:** vitrinas, catálogo, landing, captación, campañas y optimización asistida por IA.

## Componentes implementados en v0.3.0

### Tema `tiendarosso`

Tema de bloques con `theme.json`, templates, header/footer editables y una landing Gutenberg para `/vende-con-nosotros/`.

### Plugin `tiendarosso-commerce`

- Planes comerciales editables (`tr_plan`).
- Solicitudes privadas (`tr_service_request`).
- Estados: Nuevo, Contactado, Calificado, Propuesta enviada, Contratado y Perdido.
- Bloques dinámicos: comparador de planes, formulario y WhatsApp.
- Configuración administrativa de números por servicio.
- Roles: administrador, administrador comercial y comerciante mínimo.
- Registro acotado de cambios en planes y estados.
- Adaptador transaccional S3: compra directa, eventos internos, estado de pago veraz y referencia privada de vendedor por línea.

## Entidades futuras

| Entidad | Propietario | Fuente prevista |
| --- | --- | --- |
| Producto, variación, stock y pedido | WooCommerce | WooCommerce core |
| Vendedor y tienda | Motor multivendedor | Adaptador P3 |
| Membresía y comisión | Motor multivendedor | Adaptador P3 |
| Plan de servicios | TIENDA ROSSO | `tr_plan` |
| Solicitud y seguimiento | TIENDA ROSSO | `tr_service_request` |
| Campaña y presupuesto | TIENDA ROSSO | Módulo P7 separado |
| Sugerencia de IA | TIENDA ROSSO | Módulo P8 con aprobación |

## Identificadores

- Los productos usarán el ID de WooCommerce y SKU estable.
- Catálogos publicitarios enviarán `content_id` desde una única función de mapeo.
- Las solicitudes usan el ID interno de WordPress, nunca datos personales en URLs.
- Eventos navegador/servidor compartirán un `event_id` para deduplicación futura.

## Límites actuales

No existe todavía un comerciante con acceso a productos ni pedidos. El rol `tr_merchant` tiene solo `read` hasta seleccionar y probar el motor multivendedor. Esta restricción evita una falsa sensación de aislamiento.

El pedido S3 pertenece por completo a WooCommerce. No se calculan comisiones, saldos ni repartos. S6 deberá definir propietario del producto, snapshot del vendedor por línea, comisión inmutable, reembolsos parciales y visibilidad aislada antes de habilitar pedidos a comerciantes.

## Decisiones requeridas para S6

- El producto deberá tener un propietario único y cada línea conservar un snapshot privado del vendedor.
- Un pedido mixto podrá mantenerse como pedido padre, pero fulfillment, envío, devolución y visibilidad deberán separarse por comerciante.
- La responsabilidad y costo de envío deberán definirse por producto/tienda sin permitir que un vendedor modifique líneas ajenas.
- La elección final del motor multivendedor deberá respetar HPOS, Checkout Blocks, productos variables y la pasarela QR seleccionada.
- Comisiones y liquidaciones serán registros contables inmutables; no habrá reparto automático sin soporte explícito del proveedor financiero.
- Las capacidades del rol comerciante permanecerán cerradas hasta superar pruebas con dos vendedores ficticios y aislamiento de productos, clientes, pedidos y medios.
