# AGENTS.md

## Alcance

Trabajar exclusivamente dentro de este repositorio. No modificar repositorios, contenedores, bases de datos, dominios ni servicios de Docentech, Violet Flow, Rguilex u otros proyectos.

## Reglas de producto

- No inventar funcionalidades, precios, integraciones, testimonios, métricas ni clientes.
- Todo CTA debe apuntar a una ruta o ancla real, o indicar claramente que es una función pendiente.
- Mantener separados presentación (tema) y lógica comercial (plugin) cuando empiece P2.
- Gutenberg y Full Site Editing son requisitos; no incorporar Elementor.
- No enviar datos de formularios ni cargar analítica publicitaria antes de implementar consentimiento y backend seguro.
- No activar SaaS hasta verificar un pago real en fases posteriores.

## Calidad

- Ejecutar `npm.cmd run build` y `npm.cmd run test:e2e` antes de publicar cambios de P1.
- Verificar 1440 px, 1024 px, 768 px y 390 px.
- Conservar accesibilidad de teclado, foco visible y reducción de movimiento.
- Documentar cualquier capacidad simulada o pendiente.

## Git

- Rama principal: `main`.
- Commits descriptivos y sin secretos.
- Nunca usar `push --force`.

