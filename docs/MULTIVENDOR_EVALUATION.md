# Evaluación multivendedor

Fecha de investigación: 29 de octubre de 2026. Los precios son informativos y deben verificarse nuevamente antes de comprar. No se instaló ninguna extensión comercial de pago.

## Criterios

Se compararon costo, panel frontal, aprobación, productos, comisiones, membresías, compatibilidad WooCommerce/Gutenberg, seguridad reciente y carga de mantenimiento para un VPS inicial de 4 GB.

| Alternativa | Costo observado | Fortalezas | Límites y riesgo operativo | Encaje inicial |
| --- | --- | --- | --- | --- |
| Dokan Lite / Pro | Lite gratuita. Starter publicado en USD 149/año | Panel frontal consolidado, aprobación y ecosistema amplio | Funciones como variaciones pueden requerir Pro; el plan y sus módulos deben auditarse antes de comprar | Bueno si se prioriza experiencia frontal y se autoriza licencia |
| WCFM Marketplace | Núcleo gratuito. Ultimate publicado desde USD 59/año para dos sitios | Conjunto gratuito muy amplio, dashboard, membresías y muchas integraciones | Mayor superficie funcional y de configuración; su historial reciente incluye múltiples correcciones de seguridad, por lo que exige disciplina estricta de actualización y pruebas | Potente, pero no es la primera opción para un VPS pequeño sin equipo operativo dedicado |
| WC Vendors | Free disponible. Pro publicado en USD 99.50 promocional / USD 199 renovación anual | Dashboard frontal, aprobación de productos, comisiones y bloques Gutenberg; configuración relativamente acotada | Branding, envíos avanzados, membresías y algunas funciones requieren Pro/add-ons | **Recomendación para piloto P3** con Free; evaluar Pro después de probar el flujo real |
| WooCommerce Product Vendors | USD 119/año | Soporte oficial Woo, comisiones, aprobación y reportes | Administración de vendedor más cercana al escritorio WordPress; pagos automáticos centrados en PayPal Payouts; es una extensión de pago | Alternativa conservadora si se prioriza soporte oficial sobre UX frontal |

## Recomendación

Realizar un piloto aislado con **WC Vendors Free**, sin pagos reales, para comprobar:

1. Registro y aprobación manual de comerciantes.
2. Alta de producto pendiente de revisión.
3. Aislamiento de productos, pedidos, clientes y medios entre dos vendedores de prueba.
4. Compatibilidad con el tema de bloques TIENDA ROSSO y WooCommerce 11.2.1.
5. Comportamiento de productos simples y variables.
6. Consumo de memoria, consultas y caché.

No acoplar `tiendarosso-commerce` a APIs privadas de WC Vendors. Crear un adaptador en P3 para poder cambiar de motor si el piloto falla. La compra de Pro o cualquier add-on requiere autorización expresa.

## Fuentes oficiales

- Dokan pricing: https://dokan.co/wordpress/pricing/
- Dokan Lite: https://wordpress.org/plugins/dokan-lite/
- WCFM Marketplace: https://wordpress.org/plugins/wc-multivendor-marketplace/
- WCFM Ultimate: https://wclovers.com/product/woocommerce-frontend-manager-ultimate/
- WC Vendors pricing: https://www.wcvendors.com/pricing/
- WC Vendors dashboard: https://www.wcvendors.com/knowledge-base/vendor-dashboard-overview/
- WooCommerce Product Vendors: https://woocommerce.com/products/product-vendors/
- WooCommerce Product Vendors documentation: https://woocommerce.com/document/product-vendors/
