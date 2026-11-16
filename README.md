# Agenda Personal de Contactos con Autenticación en PHP

## Descripción del proyecto

Aplicación web desarrollada en PHP y MySQL que permite a los usuarios **registrarse**, **iniciar sesión** y administrar una **agenda personal de contactos** (crear, ver, editar y eliminar).

Cada usuario solo puede ver y gestionar **sus propios contactos**. Las páginas privadas verifican que exista una sesión activa, y todas las consultas a la tabla `contactos` filtran por el usuario de la sesión, por lo que no es posible acceder a contactos de otra persona cambiando el `id` en la URL.

### Funcionalidades

- Registro de usuarios con validación de correo único.
- Inicio y cierre de sesión.
- Panel privado con el nombre, el correo y la lista de contactos del usuario.
- Registrar, editar y eliminar contactos (con confirmación antes de eliminar).
- Buscador de contactos en tiempo real.

### Seguridad implementada

| Requisito | Cómo se implementó |
|---|---|
| Contraseñas protegidas | `password_hash()` al registrar y `password_verify()` al iniciar sesión. Nunca se guarda la contraseña real. |
| Validación en el servidor | Todos los formularios se validan en PHP (campos obligatorios, formato de correo, teléfono, longitudes). |
| Sesión activa en páginas protegidas | La función `requerir_sesion()` se llama al inicio de cada página privada. |
| Acceso solo a contactos propios | Las consultas usan `WHERE id = ? AND id_usuario = ?` con el id del usuario guardado en la sesión. |
| Mensajes de error seguros | El login muestra "Correo o contraseña incorrectos" sin indicar cuál dato falló. Los errores de base de datos no se muestran al usuario. |
| Inyección SQL | Todas las consultas usan sentencias preparadas con PDO. |
| XSS | Todo dato mostrado en pantalla pasa por `htmlspecialchars()`. |
| CSRF | Los formularios incluyen un token que se valida en el servidor. |
| Fijación de sesión | Se usa `session_regenerate_id(true)` al iniciar sesión. |

### Programación orientada a eventos

El archivo `assets/js/app.js` responde a eventos del usuario: `submit` (confirmación antes de eliminar y validación de formularios), e `input` (quitar mensajes de error al corregir y filtrar la tabla al escribir en el buscador). A diferencia de un programa de línea de comandos, que se ejecuta en orden de principio a fin, este código espera a que el usuario realice una acción y responde a ella.

## Tecnologías utilizadas

- PHP 8 (PDO)
- MySQL / MariaDB
- HTML5, CSS3 y JavaScript
- XAMPP (Apache + MySQL) como entorno local

## Requisitos para su ejecución

- XAMPP (o cualquier servidor con Apache, PHP 8.0 o superior y MySQL/MariaDB).
- Extensión `pdo_mysql` habilitada (viene activa en XAMPP).
- Navegador web.

## Pasos para instalar la aplicación

1. Descargar o clonar el repositorio:
   ```bash
   git clone https://github.com/USUARIO/agenda-contactos.git
   ```
2. Copiar la carpeta `agenda-contactos` dentro de `C:\xampp\htdocs\`.
3. Abrir el panel de XAMPP e iniciar **Apache** y **MySQL**.
4. Entrar a `http://localhost/phpmyadmin`, ir a la pestaña **Importar** y cargar el archivo `database/agenda.sql`. Esto crea la base de datos `agenda_contactos` con sus tablas.
5. Si tu MySQL tiene usuario o contraseña distintos, editar `config/db.php`.
6. Abrir en el navegador: `http://localhost/agenda-contactos/`
7. Crear una cuenta en **Regístrese aquí** e iniciar sesión.

## Estructura del proyecto

```
agenda-contactos/
├── assets/
│   ├── css/estilos.css        # Estilos
│   └── js/app.js              # Eventos del usuario (confirmación, validación, buscador)
├── config/
│   └── db.php                 # Conexión PDO a MySQL
├── database/
│   └── agenda.sql             # Script de la base de datos
├── includes/
│   ├── funciones.php          # Sesión, seguridad y validaciones
│   ├── header.php / footer.php
│   └── form_contacto.php      # Formulario compartido (crear/editar)
├── index.php                  # Redirige a login o panel
├── registro.php               # Registro de usuarios
├── login.php                  # Inicio de sesión
├── logout.php                 # Cierre de sesión
├── panel.php                  # Panel privado y lista de contactos
├── contacto_nuevo.php         # Crear contacto
├── contacto_editar.php        # Editar contacto
└── contacto_eliminar.php      # Eliminar contacto
```
