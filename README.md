<div align="center">
  <h1>🛡️ Sistema de Entrevista Psicológica</h1>
  <h3>Ejército Bolivariano de Venezuela</h3>
  
  [![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
  [![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)](https://codeigniter.com/)
  [![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
  [![License](https://img.shields.io/badge/License-Proprietary-important?style=for-the-badge)](LICENSE)
</div>

## 📋 Tabla de Contenidos

- [Descripción](#-descripción)
- [Características](#-características-principales)
- [Tecnologías](#-tecnologías-utilizadas)
- [Requisitos](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Documentación API](#-documentación-de-la-api)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Licencia](#-licencia)
- [Contacto](#-contacto)

## 🌟 Descripción

Sistema integral de gestión de entrevistas psicológicas desarrollado para el **Ejército Bolivariano de Venezuela**. Esta plataforma facilita a los profesionales de la psicología la realización de evaluaciones psicológicas, gestión de pacientes y generación de informes detallados de manera eficiente y segura.

## ✨ Características Principales

| Característica | Descripción |
|----------------|-------------|
| 👥 **Gestión de Usuarios** | Sistema de roles con diferentes niveles de acceso (administrador, psicólogo, etc.) |
| 🏥 **Gestión de Pacientes** | Registro completo y seguimiento detallado de pacientes |
| 📝 **Entrevistas Psicológicas** | Realización de evaluaciones psicológicas estructuradas |
| ❓ **Banco de Preguntas** | Gestión categorizada de preguntas para evaluaciones |
| 📊 **Reportes** | Generación de informes detallados y personalizados |
| 🔄 **API RESTful** | Interfaz para integración con otros sistemas |
| 🔐 **Autenticación JWT** | Seguridad robusta para todas las comunicaciones |

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.1+**
- **CodeIgniter 4** - Framework PHP
- **JWT** - Autenticación por tokens
- **Composer** - Gestión de dependencias

### Base de Datos
- **MySQL 5.7+**
- **MySQLi Driver**

### Frontend
- **HTML5**
- **CSS3**
- **JavaScript**
- **Bootstrap 5**

### Herramientas
- **Git** - Control de versiones
- **Postman** - Pruebas de API

## 📋 Requisitos del Sistema

### Servidor Web
- Apache 2.4+ o Nginx
- Módulo mod_rewrite habilitado
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Intl PHP Extension
- JSON PHP Extension
- cURL PHP Extension

### PHP 8.1 o superior
> **Nota importante sobre versiones de PHP:**
> - PHP 7.4 llegó a su fin de soporte el 28 de noviembre de 2022
> - PHP 8.0 llegó a su fin de soporte el 26 de noviembre de 2023
> - **Se recomienda actualizar inmediatamente** si está utilizando versiones anteriores a PHP 8.1
> - PHP 8.1 tendrá soporte hasta el 31 de diciembre de 2025

## 🚀 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/elpoloxrodriguez/psicologica-ejb.git
cd psicologica-ejb
```

### 2. Instalar dependencias
```bash
composer install --no-dev
```

### 3. Configuración de la base de datos
1. Crear una base de datos MySQL
2. Importar el archivo SQL inicial (si está disponible)
3. Configurar el archivo `.env`:

```env
# Configuración de la base de datos
database.default.hostname = localhost
database.default.database = nombre_base_datos
database.default.username = usuario
database.default.password = contraseña
database.default.DBDriver = MySQLi

database.tests.database = test_db
database.tests.DBDriver = MySQLi

# Configuración de la aplicación
app.baseURL = 'http://localhost:8080/'
app.indexPage = ''

# Configuración de JWT
ejwt.secret = tu_clave_secreta_muy_segura
ejwt.timeout = 3600
```

### 4. Configuración de permisos
```bash
chmod -R 755 writable/
chmod -R 775 writable/logs/
chmod -R 775 writable/uploads/
```

### 5. Ejecutar migraciones y seeders
```bash
# Ejecutar migraciones
php spark migrate

# Ejecutar seeders para datos iniciales
php spark db:seed DatabaseSeeder

# O ejecutar seeders individualmente
php spark db:seed RoleSeeder
php spark db:seed UserSeeder
```

### 6. Iniciar el servidor de desarrollo
```bash
php spark serve
```

### Credenciales por defecto

Se creará automáticamente un usuario administrador con las siguientes credenciales:

- **Email:** admin@example.com
- **Contraseña:** password

**Nota:** Asegúrate de cambiar la contraseña después del primer inicio de sesión.

## 📚 Documentación de la API

### Autenticación

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "usuario@ejemplo.com",
  "password": "contraseña"
}
```

**Respuesta exitosa (200 OK):**
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

#### Uso del token
Incluir en el encabezado de las solicitudes:
```
Authorization: Bearer [token]
```

### Endpoints Principales

#### Preguntas
- `GET /api/questions` - Listar todas las preguntas
- `POST /api/questions` - Crear nueva pregunta
- `GET /api/questions/{id}` - Obtener pregunta específica
- `PUT /api/questions/{id}` - Actualizar pregunta
- `DELETE /api/questions/{id}` - Eliminar pregunta

#### Entrevistas
- `GET /api/interviews` - Listar entrevistas
- `POST /api/interviews` - Crear nueva entrevista
- `GET /api/interviews/{id}` - Obtener entrevista específica
- `PUT /api/interviews/{id}` - Actualizar entrevista
- `DELETE /api/interviews/{id}` - Eliminar entrevista

#### Usuarios
- `GET /api/users` - Listar usuarios (admin)
- `POST /api/users` - Crear usuario (admin)
- `GET /api/users/{id}` - Obtener usuario
- `PUT /api/users/{id}` - Actualizar usuario
- `DELETE /api/users/{id}` - Eliminar usuario (admin)

#### Pacientes
- `GET /api/patients` - Listar pacientes
- `POST /api/patients` - Crear paciente
- `GET /api/patients/{id}` - Obtener paciente
- `PUT /api/patients/{id}` - Actualizar paciente
- `DELETE /api/patients/{id}` - Eliminar paciente

## 📁 Estructura del Proyecto

```
app/
├── Config/           # Configuraciones de la aplicación
│   ├── App.php      # Configuración principal
│   ├── Database.php  # Configuración de base de datos
│   └── ...
├── Controllers/      # Controladores
│   ├── API/         # Controladores de la API
│   └── ...
├── Filters/          # Filtros de autenticación
├── Helpers/          # Funciones auxiliares
├── Models/           # Modelos de la base de datos
├── Views/            # Vistas
│   ├── layouts/     # Plantillas
│   └── ...
├── Language/         # Archivos de idioma
└── ...

public/               # Archivos públicos
├── assets/          # CSS, JS, imágenes
└── index.php        # Punto de entrada

system/              # Núcleo de CodeIgniter
writable/            # Archivos generados
├── cache/          # Caché
├── logs/           # Archivos de registro
└── uploads/        # Archivos subidos
```

## 📄 Licencia

Este proyecto es propiedad exclusiva del **Ejército Bolivariano de Venezuela** y **Bunker Technologies Solutions C.A.** Todos los derechos reservados.

El uso de este software está restringido únicamente a las entidades autorizadas. Queda estrictamente prohibida la distribución, modificación o uso no autorizado.

## 📞 Contacto

### Desarrollador Principal
- **Nombre:** My. Andres Rodriguez Duran
- **Email:** [elpoloxrodriguez@gmail.com](mailto:elpoloxrodriguez@gmail.com)
- **Teléfono:** [+58 0412-9967096](tel:+584129967096)

### Bunker Technologies Solutions C.A.
- **Sitio Web:** [bunkertechsolutions.com](https://bunkertechsolutions.com)
- **Email:** [bunkertechnologiessolutions@gmail.com](mailto:bunkertechnologiessolutions@gmail.com)
- **Teléfono:** [+58 0412-2602101](tel:+584122602101)
- **RIF:** J-505619691

## 🤝 Contribuciones

Las contribuciones son bienvenidas siguiendo estos pasos:

1. Hacer fork del repositorio
2. Crear una rama para la nueva funcionalidad (`git checkout -b feature/nueva-funcionalidad`)
3. Hacer commit de los cambios (`git commit -am 'Añadir nueva funcionalidad'`)
4. Hacer push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear un nuevo Pull Request

## ⚠️ Notas Importantes

- 🔒 **Seguridad:** Este sistema está diseñado para uso exclusivo del Ejército Bolivariano de Venezuela.
- 💾 **Respaldo:** Se recomienda realizar copias de seguridad periódicas de la base de datos.
- 🚨 **Soporte Técnico:** Para reportar problemas técnicos, contactar al equipo de soporte.
- 🔄 **Actualizaciones:** Mantener el sistema actualizado a la última versión disponible.

---

<div align="center">
  <p>Desarrollado con ❤️ para el <strong>Ejército Bolivariano de Venezuela</strong></p>
  <p>© 2025 - Todos los derechos reservados</p>
</div>
