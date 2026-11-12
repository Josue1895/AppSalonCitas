# Arquitectura





## Arquitectura

### Documentación de la Arquitectura de la Aplicación

La aplicación **Relux (AppSalonCitas)** utiliza una arquitectura cliente-servidor organizada en capas, compuesta por una base de datos **MySQL** para el almacenamiento persistente de la información, una interfaz de usuario construida con **Blade, HTML, CSS/Tailwind y JavaScript (compilado con Vite)**, y un backend desarrollado en **Laravel** encargado de la lógica de negocio y el procesamiento de solicitudes.

#### Base de Datos - MySQL

La base de datos utilizada en la aplicación es MySQL, un sistema de gestión de bases de datos relacional. MySQL se encarga de almacenar y administrar de manera estructurada e íntegra los registros de usuarios, roles, servicios ofrecidos por el salón, horarios disponibles y citas agendadas, relacionando los datos mediante claves primarias y foráneas.

#### Frontend - Vistas Blade y Vite

La capa de presentación está construida utilizando el motor de plantillas **Blade** de Laravel junto con hojas de estilo CSS, JavaScript y **Vite** para la compilación de activos. Esta capa renderiza las interfaces dinámicas para los clientes (catálogo de servicios y reserva de citas) y para los administradores (gestión de agenda, servicios y usuarios), permitiendo una navegación fluida e intuitiva desde el navegador web.

#### Backend - Laravel

El backend de la aplicación está construido con **Laravel**, un framework de desarrollo web basado en PHP que sigue el patrón de diseño **Modelo-Vista-Controlador (MVC)**:

* **Modelos (`app/Models`):** Representan las entidades de la base de datos mediante Eloquent ORM y gestionan las consultas y relaciones entre tablas.
* **Vistas (`resources/views`):** Definen la estructura visual y presentan los datos enviados desde el servidor.
* **Controladores (`app/Http/Controllers`):** Reciben las peticiones HTTP, aplican validaciones y reglas de negocio (como verificar disponibilidad de horarios), interactúan con los modelos y devuelven la vista o respuesta correspondiente.

#### Flujo de Datos

El flujo de datos sigue el ciclo de solicitud-respuesta HTTP:

1. El usuario interactúa con la interfaz en el navegador (por ejemplo, al iniciar sesión o solicitar una cita) y envía una petición HTTP (`GET`, `POST`, `PUT`, `DELETE`).
2. El enrutador de Laravel (`routes/web.php` / `routes/api.php`) intercepta la URL, aplica los _middlewares_ de autenticación o permisos necesarios y dirige la petición al controlador correspondiente.
3. El controlador valida los datos recibidos, consulta o actualiza la base de datos MySQL a través de los modelos Eloquent y retorna una vista actualizada o una respuesta JSON al navegador.
