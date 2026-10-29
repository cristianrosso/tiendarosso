# Sistema de diseño

## Dirección

Editorial tecnológica: fondos claros con profundidad, tipografía expresiva, tarjetas de producto que parecen interfaces reales, bordes suaves y movimiento funcional. La composición alterna áreas de alto contraste con espacios amplios para mantener claridad comercial.

## Colores

| Token | Valor | Uso |
| --- | --- | --- |
| Violeta | `#6927D8` | Marca y acciones principales |
| Lavanda | `#9664FF` | Acento y gradientes |
| Azul | `#477BFF` | Datos, enlaces y Docentech |
| Fondo | `#F7F8FC` | Superficie principal |
| Texto | `#17172E` | Texto de alta legibilidad |
| Verde Violet Flow | `#17A875` | Acento contextual |
| Naranja Rguilex | `#F26A3D` | Acento contextual |

## Tipografía

Se usa una pila local moderna para evitar dependencias externas en P1. Los titulares emplean peso alto, interlineado compacto y escalado fluido con `clamp()`; el cuerpo mantiene un ancho de lectura controlado.

## Componentes

- Botones con estados hover, active, focus y disabled.
- Pestañas con `role=tab`, selección accesible y teclado.
- Tarjetas bento con jerarquía, no como repetición decorativa.
- Mockups conceptuales etiquetados.
- Acordeones FAQ nativos mediante `details`.
- Formularios con etiquetas visibles, ayuda y consentimiento explícito.

## Movimiento y accesibilidad

Las transiciones explican cambios de estado. `prefers-reduced-motion` elimina animaciones. El foco es visible, los objetivos táctiles superan 44 px y se conserva contraste WCAG AA en texto funcional.

## Responsive

- 1440 px: composición editorial en dos columnas y tarjetas superpuestas.
- 1024/768 px: grillas condensadas, navegación conservada y mockups simplificados.
- 390 px: una columna, CTAs de ancho completo, selector horizontal accesible y menú móvil.

