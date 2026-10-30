# Analítica y consentimiento

## Estado v0.2.0

No se cargan Google Tag Manager, GA4, Google Ads, Meta Pixel ni Meta Conversions API. No se envía información a terceros.

## Contrato futuro de eventos

| Evento | Momento único | Datos mínimos |
| --- | --- | --- |
| `solution_view` | Landing visible | solución, ruta, UTM |
| `solution_select` | Cambio explícito en el explorador | solución anterior/nueva |
| `demo_start` | Primer campo del formulario | solución, origen |
| `demo_submit` | Lead aceptado por servidor | id de oportunidad, solución |
| `checkout_start` | Checkout iniciado | producto, plan, moneda |
| `purchase` | Pago real confirmado | pedido, valor, moneda |
| `activation_complete` | SaaS activado | pedido, producto, estado |
| `whatsapp_click` | Clic en enlace configurado | contexto, servicio, UTM no sensible |
| `service_request_start` | Primera interacción con formulario | formulario, origen |
| `service_request_submit` | Solicitud aceptada por servidor | id interno, plan, servicio |

## Reglas

- Una capa de datos será la fuente única de eventos.
- `purchase` no se emite desde la página de agradecimiento sin validación del backend.
- Scripts publicitarios se bloquean hasta recibir consentimiento cuando corresponda.
- UTM se normaliza y conserva con la oportunidad/pedido, sin incluir datos sensibles.
- Las pruebas deberán detectar eventos duplicados y revocación de consentimiento.
- v0.2.0 incluye un adaptador de `dataLayer` que permanece silencioso salvo que `window.trConsent.analytics === true`.
- Un clic en WhatsApp registra contacto, nunca compra ni contratación.
- P7 añadirá `event_id` compartido entre navegador y servidor para deduplicar Meta/TikTok/Google.

