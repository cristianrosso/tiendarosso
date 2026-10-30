# Seguridad y privacidad

## Formulario comercial

- Nonce de WordPress contra CSRF.
- Honeypot y rate limit por hash temporal de dirección de red.
- Validación y sanitización del lado servidor.
- Solicitudes guardadas como posts privados, fuera de REST y búsquedas.
- Consentimiento necesario y marketing opcional almacenados por separado.
- Origen validado y UTM limitadas; no se incorporan datos personales en eventos o URLs.
- Notificación administrativa sin incluir el cuerpo completo de datos sensibles.

## Permisos

- Administrador: configuración y control total.
- Administrador comercial: planes y solicitudes; no recibe configuración global ni capacidades de sistema.
- Comerciante: rol mínimo sin acceso a productos hasta activar un adaptador multivendedor probado.
- Cliente: se utilizará el rol de WooCommerce sin ampliar capacidades globales.

## Pruebas obligatorias P3

Crear dos comerciantes y comprobar que cada uno no pueda:

- Consultar productos privados, archivos, pedidos o clientes del otro.
- Modificar precios, inventario, cupones o tienda ajena.
- Enumerar solicitudes comerciales o campañas.
- Acceder al escritorio general de WordPress cuando el dashboard frontal sea suficiente.

## Datos y retención

Desinstalar el plugin no elimina solicitudes, planes ni configuraciones. Cualquier borrado requiere una decisión explícita de retención. Producción necesitará política, exportación/borrado de datos personales, backups y cifrado de secretos fuera del repositorio.
