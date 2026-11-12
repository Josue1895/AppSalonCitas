# Api's

## Rutas de la aplicación

AppSalonCitas utiliza las rutas web de Laravel para gestionar las funcionalidades de la aplicación. Cada ruta cumple una función específica y utiliza diferentes métodos HTTP, como GET, POST y PUT.

Estas rutas permiten realizar operaciones como registrar usuarios, iniciar sesión, consultar servicios, reservar y administrar citas, configurar horarios de atención y generar reportes.

Algunas rutas requieren que el usuario haya iniciado sesión. Las rutas administrativas están protegidas mediante el middleware `admin`, que permite el acceso únicamente a usuarios con permisos de administrador.

**Nota:** Esta documentación corresponde a las rutas web definidas en `routes/web.php`. No se trata de una API REST que devuelva respuestas JSON. Las respuestas de la aplicación consisten principalmente en vistas Blade, redirecciones y archivos descargables.

### Página principal

#### GET /

```
Descripción:
Este endpoint muestra la página principal de AppSalonCitas.

Parámetros:
- Ninguno.

Respuesta:
Vista: landing.blade.php
Contenido: Página de inicio de la aplicación.
```

### Autenticación

#### GET /registro

```
Descripción:
Este endpoint muestra el formulario para registrar nuevos usuarios.

Parámetros:
- Ninguno.

Respuesta:
Vista: auth.registro
Contenido: Formulario de registro.
```

#### POST /registro

```
Descripción:
Este endpoint permite registrar un nuevo usuario en la aplicación.

Parámetros:
- nombre: Nombre del usuario (obligatorio, máximo 60 caracteres).
- apellido: Apellido del usuario (obligatorio, máximo 60 caracteres).
- email: Correo electrónico válido y único en la tabla usuarios.
- password: Contraseña (obligatoria, mínimo 6 caracteres).
- password_confirmation: Confirmación de la contraseña.
- telefono: Número de teléfono (opcional, máximo 10 caracteres).

Validaciones:
- El nombre y el apellido son obligatorios.
- El correo debe tener un formato válido y no estar registrado.
- La contraseña debe tener al menos 6 caracteres y coincidir con su confirmación.
- El teléfono es opcional.

Respuesta exitosa:
Redirección a: /login
Mensaje: Usuario registrado correctamente.
```

#### GET /login

```
Descripción:
Este endpoint muestra el formulario de inicio de sesión.

Parámetros:
- Ninguno.

Respuesta:
Vista: auth.login
Contenido: Formulario de inicio de sesión.
```

#### POST /login

```
Descripción:
Este endpoint permite iniciar sesión utilizando las credenciales del usuario.

Parámetros:
- email: Correo electrónico del usuario.
- password: Contraseña del usuario.

Validaciones:
- El correo es obligatorio y debe tener un formato válido.
- La contraseña es obligatoria y debe tener mínimo 6 caracteres.

Respuesta exitosa:
- Si el usuario es administrador, se redirige a /admin.
- Si el usuario es cliente, se redirige a /.

Mensaje: Bienvenido.

Respuesta de error:
Mensaje: Credenciales incorrectas.
Se mantiene el correo ingresado para facilitar un nuevo intento.
```

#### POST /logout

```
Descripción:
Este endpoint permite cerrar la sesión del usuario autenticado.

Parámetros:
- Ninguno.

Proceso:
- Cierra la sesión actual.
- Invalida la sesión.
- Regenera el token de sesión.

Respuesta exitosa:
Redirección a: /login
Mensaje: Sesión cerrada correctamente.
```

### Servicios

#### GET /servicios

```
Descripción:
Este endpoint muestra los servicios disponibles en AppSalonCitas.

Parámetros:
- Ninguno.

Proceso:
- Consulta los servicios que tienen el campo activo habilitado.

Respuesta:
Vista: services.index
Contenido: Listado de servicios activos.
```

### Gestión de citas

#### GET /citas

```
Descripción:
Este endpoint muestra el formulario para reservar una nueva cita.

Parámetros:
- Ninguno.

Proceso:
- Consulta los servicios activos.
- Consulta los horarios de atención.
- Organiza los horarios según el día de la semana.

Respuesta:
Vista: appointments.index
Contenido: Formulario de reserva, servicios disponibles y horarios de atención.
```

#### POST /citas

```
Descripción:
Este endpoint permite registrar una nueva cita para el usuario autenticado.

Parámetros:
- fecha: Fecha de la cita (formato YYYY-MM-DD).
- hora: Hora de inicio de la cita (formato HH:MM).
- servicios: Arreglo con los identificadores de los servicios seleccionados.

Validaciones:
- La fecha es obligatoria y no puede ser anterior al día actual.
- La hora es obligatoria y debe tener el formato HH:MM.
- Se debe seleccionar al menos un servicio.
- Los identificadores de los servicios deben existir.
- El día seleccionado debe tener atención habilitada.
- La hora debe estar dentro del horario de atención.
- La hora debe ser igual o posterior a la hora actual cuando la cita es para el mismo día.
- Los servicios seleccionados deben estar activos.
- La duración total de los servicios no debe superar el horario de cierre.
- No se permiten citas que se crucen con otros horarios ya reservados.

Proceso:
- Calcula la duración total de los servicios.
- Calcula el precio total de la cita.
- Determina la hora de finalización.
- Registra la cita y los servicios seleccionados.
- Guarda el precio individual de cada servicio en la relación de la cita.
- Intenta enviar un correo de confirmación al cliente.

Respuesta exitosa:
Redirección a: /mis-citas
Mensaje: Cita reservada correctamente. Te enviamos la confirmación a tu correo.

Nota:
Si ocurre un problema con el envío del correo, la cita permanece registrada y se muestra un mensaje informando que no se pudo enviar la confirmación.

Si el usuario no ha iniciado sesión, se redirige a /login.
```

#### GET /mis-citas

```
Descripción:
Este endpoint muestra las citas registradas por el usuario autenticado.

Parámetros:
- Ninguno.

Proceso:
- Consulta las citas asociadas al usuario.
- Ordena las citas por fecha y hora.

Respuesta:
Vista: appointments.mis-citas
Contenido: Listado de citas del usuario.

Restricción:
Requiere iniciar sesión.
```

#### GET /mis-citas/{id}/editar

```
Descripción:
Este endpoint muestra el formulario para editar una cita existente.

Parámetros:
- id: Identificador de la cita que se desea editar.

Restricciones:
- La cita debe pertenecer al usuario autenticado.
- Solamente se pueden editar citas cuyo estado sea pendiente.

Proceso:
- Consulta la cita seleccionada.
- Recupera los servicios asociados.
- Obtiene los servicios activos y los servicios que ya pertenecen a la cita.

Respuesta:
Vista: appointments.edit
Contenido: Formulario con los datos de la cita y sus servicios.

Respuesta de error:
Si la cita no pertenece al usuario o no está pendiente, se redirige a /mis-citas con un mensaje de error.
```

#### PUT /mis-citas/{id}

```
Descripción:
Este endpoint permite actualizar una cita existente.

Parámetros:
- id: Identificador de la cita.
- fecha: Nueva fecha de la cita (formato YYYY-MM-DD).
- hora: Nueva hora de inicio (formato HH:MM).
- servicios: Arreglo con los identificadores de los servicios seleccionados.

Validaciones:
- Se aplican las mismas validaciones de fecha, hora y servicios utilizadas al reservar una cita.
- La cita debe pertenecer al usuario autenticado.
- La cita debe encontrarse en estado pendiente.
- Se verifica nuevamente la disponibilidad del horario.
- Se calcula el nuevo precio y la duración de los servicios seleccionados.

Proceso:
- Actualiza los datos de la cita.
- Reemplaza los servicios asociados por los nuevos servicios seleccionados.
- Actualiza el precio total y la hora de finalización.

Respuesta exitosa:
Redirección a: /mis-citas
Mensaje: Cita actualizada correctamente.
```

#### POST /mis-citas/cancelar/{id}

```
Descripción:
Este endpoint permite cancelar una cita registrada por el usuario.

Parámetros:
- id: Identificador de la cita que se desea cancelar.

Restricciones:
- El usuario debe haber iniciado sesión.
- La cita debe pertenecer al usuario autenticado.
- Solamente se pueden cancelar citas cuyo estado sea pendiente.

Proceso:
- Cambia el estado de la cita a cancelada.

Respuesta exitosa:
Redirección a: /mis-citas
Mensaje: Cita cancelada correctamente.
```

### Recuperación de contraseña

#### GET /forgot-password

```
Descripción:
Este endpoint muestra el formulario para solicitar la recuperación de contraseña.

Parámetros:
- Ninguno.

Respuesta:
Vista: auth.forgot-password
Contenido: Formulario para ingresar el correo electrónico.
```

#### POST /forgot-password

```
Descripción:
Este endpoint permite solicitar un enlace para restablecer la contraseña del usuario.

Parámetros:
- email: Correo electrónico asociado a la cuenta.

Validaciones:
- El correo electrónico es obligatorio.
- Debe tener un formato válido.
- Debe existir una cuenta asociada al correo.

Proceso:
- Genera un token aleatorio de recuperación.
- Guarda el token cifrado junto con el correo y la fecha de creación.
- Genera un enlace de recuperación.
- Envía el enlace al correo electrónico del usuario.

Respuesta exitosa:
Mensaje: Se ha enviado un enlace de recuperación a su correo.

Respuesta de error:
Si no existe una cuenta asociada al correo, se muestra un mensaje de error.
```

#### GET /reset-password/{token}

```
Descripción:
Este endpoint muestra el formulario para establecer una nueva contraseña.

Parámetros:
- token: Token de recuperación incluido en el enlace.
- email: Correo electrónico del usuario (parámetro de consulta).

Respuesta:
Vista: auth.reset-password
Contenido: Formulario para ingresar y confirmar la nueva contraseña.
```

#### POST /reset-password

```
Descripción:
Este endpoint permite actualizar la contraseña utilizando el token de recuperación.

Parámetros:
- email: Correo electrónico del usuario.
- token: Token de recuperación recibido.
- password: Nueva contraseña (mínimo 6 caracteres).
- password_confirmation: Confirmación de la nueva contraseña.

Validaciones:
- El correo debe ser válido.
- El token es obligatorio y debe coincidir con el token almacenado.
- La contraseña debe tener mínimo 6 caracteres.
- La contraseña debe coincidir con su confirmación.
- Debe existir una cuenta asociada al correo.

Proceso:
- Verifica el token de recuperación.
- Actualiza la contraseña del usuario.
- Elimina el registro del token utilizado.

Respuesta exitosa:
Redirección a: /login
Mensaje: Contraseña actualizada correctamente.
```

### Administración

Las siguientes rutas se encuentran protegidas por el middleware `admin`. Esto significa que solamente los usuarios con permisos de administrador pueden acceder a estas funcionalidades.

#### GET /admin

```
Descripción:
Este endpoint muestra el panel principal de administración.

Parámetros:
- Ninguno.

Proceso:
- Consulta el total de usuarios.
- Consulta el total de servicios.
- Consulta el total de citas.
- Obtiene las citas del día.
- Calcula las citas completadas y los ingresos totales.
- Consulta las citas por estado.
- Obtiene las próximas cinco citas y las cinco más recientes.

Respuesta:
Vista: admin.index
Contenido: Panel principal con indicadores y actividad del sistema.
```

#### GET /admin/usuarios

```
Descripción:
Este endpoint muestra los usuarios registrados y permite realizar búsquedas.

Parámetros:
- buscar: Texto opcional para buscar usuarios por nombre, apellido o correo electrónico (máximo 100 caracteres).

Proceso:
- Consulta los usuarios registrados.
- Aplica el filtro de búsqueda cuando se proporciona.
- Ordena los resultados por nombre y apellido.
- Presenta los resultados en páginas de 15 usuarios.

Respuesta:
Vista: admin.usuarios
Contenido: Listado de usuarios y sus roles.
```

#### PUT /admin/usuarios/{id}/rol

```
Descripción:
Este endpoint permite asignar o quitar el permiso de administrador a un usuario.

Parámetros:
- id: Identificador del usuario.
- admin: Valor booleano que indica si el usuario tendrá permisos de administrador (1 o 0).

Validaciones y restricciones:
- El campo admin es obligatorio y debe ser booleano.
- Un administrador no puede quitarse a sí mismo ese permiso.
- Siempre debe permanecer al menos un administrador en el sistema.
- Si el usuario ya tiene el permiso solicitado, no se realiza ningún cambio.

Respuesta exitosa:
Redirección a: /admin/usuarios
Mensaje: El rol del usuario se actualizó correctamente.
```

#### GET /admin/servicios

```
Descripción:
Este endpoint muestra todos los servicios registrados para su administración.

Parámetros:
- Ninguno.

Proceso:
- Consulta todos los servicios registrados, tanto activos como inactivos.

Respuesta:
Vista: admin.servicios.index
Contenido: Listado administrativo de servicios.
```

#### GET /admin/servicios/crear

```
Descripción:
Este endpoint muestra el formulario para registrar un nuevo servicio.

Parámetros:
- Ninguno.

Respuesta:
Vista: admin.servicios.create
Contenido: Formulario de creación de servicios.
```

#### POST /admin/servicios

```
Descripción:
Este endpoint permite crear un nuevo servicio en la aplicación.

Parámetros:
- nombre: Nombre del servicio (obligatorio, máximo 60 caracteres).
- precio: Precio del servicio (obligatorio y numérico).
- descripcion: Descripción del servicio (opcional).
- duracion: Duración del servicio en minutos (obligatoria y entera).

Proceso:
- Valida los datos recibidos.
- Registra el servicio.
- Establece el servicio como activo.

Respuesta exitosa:
Redirección a: /admin/servicios
Mensaje: Servicio creado correctamente.
```

#### GET /admin/servicios/editar/{id}

```
Descripción:
Este endpoint muestra el formulario para editar un servicio existente.

Parámetros:
- id: Identificador del servicio.

Proceso:
- Busca el servicio solicitado.
- Recupera sus datos para mostrarlos en el formulario.

Respuesta:
Vista: admin.servicios.edit
Contenido: Formulario con la información del servicio seleccionado.
```

#### POST /admin/servicios/actualizar/{id}

```
Descripción:
Este endpoint permite actualizar la información de un servicio existente.

Parámetros:
- id: Identificador del servicio.
- nombre: Nombre del servicio (obligatorio, máximo 60 caracteres).
- precio: Precio del servicio (obligatorio y numérico).
- descripcion: Descripción del servicio (opcional).
- duracion: Duración del servicio en minutos (obligatoria y entera).

Proceso:
- Valida los datos recibidos.
- Busca el servicio seleccionado.
- Actualiza su nombre, precio, descripción y duración.

Respuesta exitosa:
Redirección a: /admin/servicios
Mensaje: Servicio actualizado correctamente.
```

#### POST /admin/servicios/eliminar/{id}

```
Descripción:
Este endpoint permite eliminar un servicio del sistema.

Parámetros:
- id: Identificador del servicio que se desea eliminar.

Proceso:
- Busca el servicio seleccionado.
- Ejecuta la operación de eliminación.

Respuesta exitosa:
Redirección a: /admin/servicios
Mensaje: Servicio eliminado correctamente.

Nota:
La eliminación puede estar sujeta a las restricciones de integridad referencial de la base de datos.
```

#### GET /admin/citas

```
Descripción:
Este endpoint muestra todas las citas registradas para su gestión administrativa.

Parámetros:
- Ninguno.

Proceso:
- Consulta las citas junto con la información del usuario asociado.
- Ordena las citas por fecha y hora.

Respuesta:
Vista: admin.citas
Contenido: Listado administrativo de citas.
```

#### POST /admin/citas/estado/{id}

```
Descripción:
Este endpoint permite actualizar el estado de una cita desde el panel administrativo.

Parámetros:
- id: Identificador de la cita.
- estado: Nuevo estado de la cita. Los valores permitidos son:
  - pendiente
  - confirmada
  - completada
  - cancelada

Proceso:
- Valida el nuevo estado.
- Recupera la cita y la información asociada.
- Actualiza el estado de la cita.
- Si el estado cambia y el cliente tiene un correo registrado, intenta enviar una notificación por correo electrónico.

Respuesta exitosa:
Redirección a: /admin/citas
Mensaje: Estado actualizado correctamente.

Nota:
Si el envío del correo falla, el cambio de estado se conserva y se muestra un mensaje informando del problema.
```

### Configuración de horarios

#### GET /admin/horarios

```
Descripción:
Este endpoint muestra la configuración de los horarios de atención del establecimiento.

Parámetros:
- Ninguno.

Proceso:
- Consulta los horarios registrados.
- Los organiza por día de la semana.

Respuesta:
Vista: admin.horarios
Contenido: Configuración de horarios de lunes a domingo.
```

#### POST /admin/horarios

```
Descripción:
Este endpoint permite actualizar los horarios de atención del establecimiento.

Parámetros:
- horarios: Arreglo con la configuración de los siete días de la semana.
- horarios[día].activo: Indica si el establecimiento atenderá ese día (booleano).
- horarios[día].hora_inicio: Hora de apertura (formato HH:MM).
- horarios[día].hora_fin: Hora de cierre (formato HH:MM).

Validaciones:
- Se debe enviar la configuración de los siete días.
- Cada día debe incluir el campo activo.
- Las horas deben tener un formato válido cuando se proporcionan.
- Si un día está activo, debe tener una hora de apertura y una hora de cierre.
- La hora de apertura debe ser anterior a la hora de cierre.

Proceso:
- Valida la configuración recibida.
- Actualiza los horarios de cada día.
- Si un día está desactivado, guarda las horas como 00:00:00.

Respuesta exitosa:
Redirección a: /admin/horarios
Mensaje: Los horarios se actualizaron correctamente.
```

### Reportes

#### GET /admin/reportes

```
Descripción:
Este endpoint muestra los reportes administrativos de citas e ingresos.

Parámetros:
- fecha_inicio: Fecha inicial del filtro (opcional, formato YYYY-MM-DD).
- fecha_fin: Fecha final del filtro (opcional, formato YYYY-MM-DD).

Validaciones:
- Las fechas deben tener el formato YYYY-MM-DD.
- La fecha final debe ser igual o posterior a la fecha inicial.

Proceso:
- Consulta las citas dentro del rango de fechas seleccionado.
- Calcula el total de citas.
- Cuenta las citas completadas, canceladas, pendientes y confirmadas.
- Calcula los ingresos de las citas completadas.
- Agrupa los ingresos por fecha.
- Obtiene los cinco servicios más solicitados, excluyendo las citas canceladas.

Respuesta:
Vista: admin.reportes
Contenido:
- Listado de citas.
- Total de citas.
- Cantidad de citas por estado.
- Ingresos totales.
- Ingresos por fecha.
- Servicios más solicitados.
```

#### GET /admin/reportes/pdf

```
Descripción:
Este endpoint genera un reporte de citas en formato PDF para su descarga.

Parámetros:
- fecha_inicio: Fecha inicial del filtro (opcional, formato YYYY-MM-DD).
- fecha_fin: Fecha final del filtro (opcional, formato YYYY-MM-DD).

Validaciones:
- Las fechas deben tener el formato YYYY-MM-DD.
- La fecha final debe ser igual o posterior a la fecha inicial.

Proceso:
- Consulta las citas según el rango de fechas.
- Recupera los servicios asociados y sus precios históricos.
- Calcula los ingresos de las citas completadas.
- Cuenta las citas completadas y canceladas.
- Genera el documento PDF utilizando la vista de reportes.

Respuesta exitosa:
Archivo descargable: reporte-citas.pdf
Formato: PDF.
```

#### GET /admin/reportes/excel

```
Descripción:
Este endpoint genera un reporte de citas en formato Excel para su descarga.

Parámetros:
- fecha_inicio: Fecha inicial del filtro (opcional, formato YYYY-MM-DD).
- fecha_fin: Fecha final del filtro (opcional, formato YYYY-MM-DD).

Validaciones:
- Las fechas deben tener el formato YYYY-MM-DD.
- La fecha final debe ser igual o posterior a la fecha inicial.

Proceso:
- Recibe los filtros de fecha.
- Genera la exportación de citas mediante la clase CitasExport.
- Prepara el archivo Excel para su descarga.

Respuesta exitosa:
Archivo descargable: reporte-citas.xlsx
Formato: XLSX.
```

### Consideraciones generales

* Las rutas de autenticación permiten registrar usuarios, iniciar sesión y cerrar sesión.
* Las operaciones de reserva, edición y cancelación de citas requieren que el usuario esté autenticado.
* Cada usuario solamente puede gestionar sus propias citas.
* Las citas solamente pueden editarse o cancelarse cuando su estado es pendiente.
* Las rutas administrativas están protegidas mediante el middleware `admin`.
* Los cambios de estado de las citas pueden generar notificaciones por correo electrónico.
* La disponibilidad de las citas depende de los horarios configurados y de las reservas existentes.
* Los reportes permiten filtrar la información por fechas y descargarla en PDF o Excel.
* Las rutas descritas están definidas en el archivo `routes/web.php`.
* Las respuestas corresponden al funcionamiento web de Laravel y no a respuestas JSON de una API REST.
