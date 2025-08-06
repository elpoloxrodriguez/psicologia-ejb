# Sistema de Entrevista Psicológica - Ejército Bolivariano de Venezuela

![Banner del Sistema](https://via.placeholder.com/1200x400?text=Sistema+de+Entrevista+Psicológica)

## 🎯 Descripción

Sistema de gestión de entrevistas psicológicas desarrollado para el Ejército Bolivariano de Venezuela. Esta plataforma permite a los profesionales de la psicología realizar evaluaciones, gestionar pacientes y generar informes detallados de manera eficiente.

## ✨ Características Principales

- **Gestión de Usuarios**: Diferentes roles (administrador, psicólogo, etc.)
- **Gestión de Pacientes**: Registro y seguimiento de pacientes
- **Entrevistas Psicológicas**: Realización de evaluaciones psicológicas estructuradas
- **Banco de Preguntas**: Gestión de preguntas y categorías
- **Reportes**: Generación de informes detallados
- **API RESTful**: Interfaz para integración con otros sistemas
- **Autenticación JWT**: Seguridad robusta para las comunicaciones

## 🚀 Tecnologías Utilizadas

- **Backend**: PHP 8.1+
- **Framework**: CodeIgniter 4
- **Base de Datos**: MySQL
- **Autenticación**: JWT (JSON Web Tokens)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Herramientas**: Composer, Git

## 📝 Requisitos del Sistema

- PHP 8.1 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx) con mod_rewrite habilitado
- Extensión intl de PHP
- Extensión mbstring de PHP
- Composer (para la gestión de dependencias)

## 🔧 Instalación

1. Clonar el repositorio:
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   cd nombre-del-proyecto
   ```

2. Instalar dependencias:
   ```bash
   composer install
   ```

3. Configurar la base de datos:
   - Crear una base de datos MySQL
   - Importar el archivo SQL inicial (si existe)

4. Configurar el archivo `.env`:
   ```env
   database.default.hostname = localhost
   database.default.database = nombre_base_datos
   database.default.username = usuario
   database.default.password = contraseña
   database.default.DBDriver = MySQLi
   
   app.baseURL = 'http://localhost:8080/'
   ```

5. Configurar los permisos de los directorios:
   ```bash
   chmod -R 755 writable/
   ```

6. Iniciar el servidor de desarrollo:
   ```bash
   php spark serve
   ```

## 🔐 API Endpoints

### Autenticación
- `POST /api/auth/register` - Registrar nuevo usuario
- `POST /api/auth/login` - Iniciar sesión

### Preguntas
- `GET /api/questions` - Listar todas las preguntas
- `POST /api/questions` - Crear nueva pregunta
- `GET /api/questions/(:segment)` - Ver detalle de pregunta
- `PUT /api/questions/(:segment)` - Actualizar pregunta
- `DELETE /api/questions/(:segment)` - Eliminar pregunta

### Entrevistas
- `GET /api/interviews` - Listar entrevistas
- `GET /api/interviews/check` - Verificar estado de entrevista
- `GET /api/interviews/questions` - Obtener preguntas para entrevista
- `POST /api/interviews` - Crear nueva entrevista
- `GET /api/interviews/(:segment)` - Ver detalle de entrevista
- `DELETE /api/interviews/(:segment)` - Eliminar entrevista

### Usuarios
- `GET /api/users` - Listar usuarios (solo admin)
- `POST /api/users` - Crear usuario (solo admin)
- `GET /api/users/(:segment)` - Ver detalle de usuario
- `PUT /api/users/(:segment)` - Actualizar usuario
- `DELETE /api/users/(:segment)` - Eliminar usuario (solo admin)

### Pacientes
- `GET /api/patients` - Listar pacientes
- `POST /api/patients` - Crear paciente
- `GET /api/patients/(:segment)` - Ver detalle de paciente
- `PUT /api/patients/(:segment)` - Actualizar paciente
- `DELETE /api/patients/(:segment)` - Eliminar paciente

## 📝 Uso de la API

### Autenticación

1. **Login**
   ```http
   POST /api/auth/login
   Content-Type: application/json
   
   {
     "email": "usuario@ejemplo.com",
     "password": "contraseña"
   }
   ```
   
   Respuesta exitosa:
   ```json
   {
     "status": true,
     "message": "Inicio de sesión exitoso",
     "data": {
       "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
       "user": {
         "id": 1,
         "name": "Nombre Usuario",
         "email": "usuario@ejemplo.com",
         "role": "psychologist"
       }
     }
   }
   ```

2. **Uso del token**
   Incluir el token en el encabezado de las solicitudes:
   ```
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
   ```

## 📊 Estructura del Proyecto

```
app/
├── Config/           # Archivos de configuración
├── Controllers/      # Controladores de la aplicación
│   └── API/          # Controladores de la API
├── Filters/          # Filtros de autenticación
├── Helpers/          # Funciones auxiliares
├── Models/           # Modelos de la base de datos
├── Views/            # Vistas de la aplicación
public/               # Archivos públicos
system/               # Núcleo de CodeIgniter
writable/             # Archivos generados
```

## 📄 Licencia

Este proyecto es propiedad del Ejército Bolivariano de Venezuela y Bunker Technologies Solutions C.A.

## 📞 Contacto

- **Desarrollador Principal**: My. Andres Rodriguez Duran
- **Email**: elpoloxrodriguez@gmail.com
- **Teléfono**: +58 0412-9967096

### Bunker Technologies Solutions C.A.
- **Sitio Web**: [bunkertechsolutions.com](https://bunkertechsolutions.com)
- **Email**: bunkertechnologiessolutions@gmail.com
- **Teléfono**: +58 0412-2602101
- **RIF**: J-505619691

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, lea las pautas de contribución antes de enviar cambios.

## 📌 Notas Adicionales

- Este sistema está diseñado para uso exclusivo del Ejército Bolivariano de Venezuela.
- Se recomienda realizar copias de seguridad periódicas de la base de datos.
- Para problemas técnicos, por favor contactar al equipo de soporte.
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
