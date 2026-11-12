# Arquitectura

## Arquitectura del sistema

### 1. Introducción

AppSalonCitas (RELUX) es una aplicación web para gestionar la reserva de citas de un salón de belleza. Su funcionamiento permite que los clientes consulten los servicios disponibles, soliciten citas y consulten sus reservas. También cuenta con un área administrativa para gestionar usuarios, servicios, citas, horarios y reportes.

El proyecto está desarrollado siguiendo la arquitectura MVC de Laravel, separando las responsabilidades entre modelos, vistas, controladores y solicitudes de validación.

### 2. Arquitectura general

La aplicación se organiza en los siguientes componentes:

* **Rutas:** reciben las solicitudes de los usuarios y las dirigen a las acciones correspondientes.
* **Middleware:** controla el acceso a las funciones administrativas.
* **Controladores:** gestionan la lógica de las operaciones del sistema.
* **Modelos:** representan la información y las relaciones de la aplicación.
* **Vistas:** presentan las páginas y formularios.
* **Solicitudes de validación:** comprueban los datos enviados por los usuarios.
* **Base de datos:** almacena usuarios, servicios, citas y horarios.

### 3. Organización del proyecto

#### Controladores

En `app/Http/Controllers` se encuentran:

* `AdminController.php`
* `AppointmentController.php`
* `AuthController.php`
* `Controller.php`
* `PasswordResetController.php`
* `ReportController.php`
* `ServiceController.php`

#### Modelos

En `app/Models` se encuentran:

* `Appointment.php`
* `AppointmentService.php`
* `Horario.php`
* `Service.php`
* `User.php`

#### Middleware

* `AdminMiddleware.php`: participa en la protección de las funciones administrativas.

#### Solicitudes de validación

En `app/Http/Requests` se encuentran:

* `AppointmentRequest.php`
* `LoginRequest.php`
* `RegisterRequest.php`

### 4. Componentes principales

#### Autenticación

El sistema permite registrar usuarios, iniciar sesión, cerrar sesión y recuperar contraseñas. La aplicación diferencia el acceso de clientes y administradores.

#### Gestión de citas

Los clientes pueden solicitar citas seleccionando servicios, fecha y hora. El sistema valida los datos, consulta los horarios de atención y administra los estados de las citas.

#### Gestión de servicios

El área administrativa permite consultar, crear, editar y eliminar servicios.

#### Gestión de horarios

Los horarios se administran por día de la semana. Cada registro contiene el día, la hora de inicio, la hora de finalización y su estado de actividad.

#### Administración

El área administrativa incluye gestión de usuarios, citas, servicios y horarios.

#### Reportes

El sistema cuenta con reportes y opciones de descarga en PDF y Excel.

### 5. Modelos y relaciones

Las relaciones declaradas en los modelos son:

* Un usuario puede tener muchas citas.
* Una cita pertenece a un usuario.
* Una cita pertenece a un horario.
* Una cita puede tener varios registros de servicios asociados.
* Cada registro de servicios asociados pertenece a una cita.
* Cada registro de servicios asociados pertenece a un servicio.
* Un servicio puede estar asociado a muchos registros de servicios de citas.

Estas relaciones permiten organizar las reservas y los servicios seleccionados por cada cliente.

### 6. Base de datos

La estructura de datos del sistema se construye mediante migraciones.

Las tablas principales identificadas son:

* `usuarios`
* `servicios`
* `citas`
* `citas_servicios`
* `horarios`

También existen tablas de soporte utilizadas por el funcionamiento de Laravel, entre ellas las relacionadas con usuarios del framework, caché y tareas en cola.

La tabla `citas_servicios` permite relacionar las citas con los servicios seleccionados y almacenar el precio unitario correspondiente.

Las reglas de integridad referencial contemplan la relación entre usuarios y citas, horarios y citas, citas y sus servicios, y servicios y sus registros asociados.

### 7. Diagrama de arquitectura

Para representar la arquitectura general utilizamos GitDiagram. El diagrama muestra los actores, las rutas web, los procesos de clientes y administradores, los modelos y la conexión con MySQL.

Este diagrama representa la organización general de la aplicación. No sustituye el esquema detallado de la base de datos ni muestra todas las columnas y claves foráneas.

### 8. Conclusión

La arquitectura de AppSalonCitas organiza las funciones del sistema en componentes separados, facilitando el mantenimiento y la comprensión del proyecto. La separación entre rutas, controladores, modelos, vistas y validaciones permite mantener una estructura ordenada y coherente con el desarrollo realizado.
