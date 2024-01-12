# Framework Personalizado para Elitenut

Este proyecto es un framework personalizado diseñado específicamente para desarrollos de la empresa Elitenut. Utiliza Slim como framework de rutas, proporcionando una estructura ligera y eficiente para la creación de aplicaciones web y APIs.

## Características Principales

- **Framework de Rutas Slim**: Usa Slim 2.6, un micro framework de PHP, para manejar las rutas de manera eficiente y sencilla.
- **Personalizado para Elitenut**: Diseñado con las necesidades específicas de Elitenut en mente, asegurando un ajuste perfecto para los proyectos de la empresa.


## Fichero .env

Se debe crear un fichero .env para las conexiones con la base de datos y credenciales para enviar correos

DB_HOST=localhost
DB_USER=
DB_PASS=
DB_NAME=

KEY_SECRET= llave secreta para generar los jwt
KEY_HEADER= Authentication basic para los endpoint

FILE_JSON=public/json/productos.json
FILE_USERS_JSON=public/json/users.json
PATH_UPLOAD_IMAGES=public/uploads/images/

MAIL_MAILER=smtp
MAIL_HOST=smtp.googlemail.com
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="NAME APP"


## Comandos Disponibles

Este framework incluye una serie de comandos útiles para facilitar el desarrollo y mantenimiento de tus aplicaciones:

### Actualizar Autoload

Cuando se añaden nuevos namespaces o clases, es necesario actualizar el autoload para reflejar estos cambios. Esto se puede hacer con el siguiente comando:

`composer dump-autoload`

### Actualizar Paquetes

Para mantener los paquetes utilizados en el proyecto actualizados, ejecuta:

`composer upddate`




Este comando buscará las últimas versiones de los paquetes definidos en tu `composer.json` y los actualizará si es necesario.

## Contribuciones

Las contribuciones al framework son bienvenidas, siempre y cuando se alineen con las necesidades y estándares de Elitenut.

---

Desarrollado con ❤️ por el equipo de Elitenut.
