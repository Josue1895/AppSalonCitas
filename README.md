# AppSalonCitas

Sistema web para la gestión de citas en un salón de belleza, desarrollado con Laravel bajo arquitectura MVC.

---

## Tecnologías utilizadas

- Laravel 11
- PHP 8.2
- MySQL (phpMyAdmin)
- Blade
- Eloquent ORM

---

## Funcionalidades

### Usuarios
- Registro de usuarios
- Inicio de sesión
- Cierre de sesión

### Servicios
- Ver servicios
- Crear servicios (admin)
- Editar servicios (admin)
- Eliminar servicios (admin)

### Citas
- Reservar citas
- Ver mis citas
- Cancelar citas

### Administración
- Panel admin
- Gestión de servicios
- Gestión de citas
- Cambio de estado de citas

---

## Base de datos

Nombre de la base de datos:

appsalonCitas_mvc_php


Se crea mediante migraciones de Laravel.

---

## Cómo ejecutar el proyecto

1. Ubicar el proyecto en:

C:\xampp\htdocs\appsalonCitas


2. Crear la base de datos en phpMyAdmin:

appsalonCitas_mvc_php


3. Configurar el archivo `.env`:


DB_DATABASE=appsalonCitas_mvc_php
DB_USERNAME=root
DB_PASSWORD=


4. Ejecutar:


php artisan migrate:fresh --seed


5. Iniciar el proyecto:


php artisan serve


---

## Credenciales de prueba

### Administrador
Correo: admin@appsalon.com  
Contraseña: 123456  

### Usuario
Correo: cliente@appsalon.com  
Contraseña: 123456  

---

## Rutas principales

- `/` → Inicio
- `/login` → Iniciar sesión
- `/registro` → Registro
- `/citas` → Reservar cita
- `/mis-citas` → Ver citas
- `/admin` → Panel admin

---

## Seguridad implementada

- Contraseñas hasheadas
- Validación de formularios
- Middleware para administrador
- Protección CSRF
- Sanitización básica

---

## Autor

Proyecto desarrollado por Josue Guzman.
