# Módulos futuros de inteligencia artificial

La IA se integrará como servicio opcional y desacoplado. Ninguna salida publica contenido, modifica precios, ejecuta campañas o consume presupuesto sin aprobación.

| Módulo | Datos permitidos | Salida | Aprobación |
| --- | --- | --- | --- |
| Asistente de compra | Catálogo público y disponibilidad real | Productos relevantes y respuestas citadas | No publica cambios |
| Asistente de comerciante | Datos del propio negocio/producto | Borradores de títulos, descripciones y etiquetas | Comerciante o administrador |
| Recomendación | Categoría, etiquetas e interacciones consentidas | Productos relacionados | Regla configurable |
| Analizador comercial | Métricas agregadas del propio negocio | Hipótesis y oportunidades | Administrador comercial |
| Generador de campañas | Catálogo aprobado y brief | Borradores de anuncios y landings | Responsable de campaña |
| Recuperación | Oportunidades consentidas | Propuesta de seguimiento | Envío manual o automatización autorizada |

## Controles

- Proveedor intercambiable mediante interfaz.
- Claves solo en secretos del entorno.
- Límites por usuario, plan y periodo.
- Registro de modelo, costo, entrada resumida, salida y aprobación.
- Redacción de datos personales antes de llamar a un proveedor externo.
- Estado `borrador → revisado → aprobado → publicado`.
- Circuit breaker y presupuesto máximo; sin reintentos ilimitados.
