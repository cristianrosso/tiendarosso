# Embudos comerciales

## Venta asistida — objetivo inicial

```text
Campaña o búsqueda → Landing de solución → Solicitud de demo
→ Consentimiento y registro seguro → Seguimiento comercial → Contratación
```

La vista estática implementa solo la confirmación visual y no envía datos. v0.2.0 conecta el bloque WordPress con nonce, honeypot, rate limit, post privado, notificación administrativa y estados de seguimiento.

Campos previstos: nombre, correo, WhatsApp, empresa, solución, plan, origen, UTM y consentimiento. El teléfono será opcional hasta definir la política comercial.

## Venta directa — fase futura

```text
Campaña → Landing → Plan → Checkout WooCommerce
→ Pago confirmado → Cola de activación → API autenticada del SaaS
```

La activación debe empezar únicamente con un estado de pago confirmado e idempotente. No se enviarán contraseñas por correo.

## Destinos de CTA en P1

- Explorar soluciones → selector de portada.
- Solicitar demostración → formulario local rotulado como prototipo.
- Conocer solución → landing correspondiente.
- Hablar sobre un plan → formulario con solución preseleccionada.

## Servicios para negocios

```text
Campaña o búsqueda → /vende-con-nosotros/ → Comparación de planes
→ Formulario o WhatsApp configurado → Solicitud privada → Seguimiento → Contratación
```

Los honorarios del servicio y el presupuesto publicitario se mantienen separados. WhatsApp es un contacto, no una contratación.

