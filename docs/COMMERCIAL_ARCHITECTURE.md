# Arquitectura comercial

## Líneas de negocio

1. **Marketplace:** categorías, negocios, productos físicos/digitales, consultas y futuros pedidos.
2. **SaaS propio:** Docentech, Violet Flow y Rguilex con landings y demostraciones verificables.
3. **Servicios para negocios:** vitrinas, catálogo, landing, captación, campañas y optimización asistida por IA.

## Componentes implementados en v0.2.0

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
