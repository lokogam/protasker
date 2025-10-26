# 🔧 Protasker Backend API

**Laravel 12 REST API with Sanctum Authentication**

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)

## 📋 Descripción

API REST desarrollada con Laravel 12 que proporciona endpoints para la gestión de proyectos, tareas y usuarios con autenticación basada en tokens.

## 🛠️ Stack Tecnológico

- **Laravel 12**: Framework PHP robusto
- **PHP 8.2+**: Lenguaje de programación moderno
- **MySQL 8.0**: Base de datos relacional
- **Laravel Sanctum**: Autenticación API sin estado
- **Spatie Permission**: Gestión de roles y permisos
- **Eloquent ORM**: Mapeo objeto-relacional
- **Form Requests**: Validaciones robustas

## 🚀 Instalación

### Con Docker (Recomendado)
```bash
# Desde la raíz del proyecto
./docker.sh setup
```

### Manual
```bash
cd protasker-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

## 📜 Comandos Artisan

```bash
# Desarrollo
php artisan serve              # Servidor de desarrollo
php artisan migrate:fresh --seed  # Reset BD con datos
php artisan tinker            # REPL interactivo

# Testing
php artisan test              # Ejecutar tests
php artisan test --coverage  # Tests con cobertura

# Optimización
php artisan optimize          # Optimizar para producción
php artisan config:cache     # Cachear configuración
php artisan route:cache      # Cachear rutas
```

## 🗄️ Modelo de Datos

### Entidades Principales

```php
// User
- id, name, email, password
- timestamps, email_verified_at
- Roles: admin, developer

// Project  
- id, name, description, user_id
- progress (calculado), timestamps
- belongsTo User, hasMany Tasks

// Task
- id, title, description, status
- project_id, assigned_user_id
- timestamps
- belongsTo Project, belongsTo User
```

### Relaciones

```php
User::class
├─ hasMany(Project::class)
└─ hasMany(Task::class, 'assigned_user_id')

Project::class  
├─ belongsTo(User::class)
└─ hasMany(Task::class)

Task::class
├─ belongsTo(Project::class)
└─ belongsTo(User::class, 'assigned_user_id')
```

## 🔐 Autenticación

### Laravel Sanctum
```php
// Login
POST /api/auth/login
{
    "email": "admin@example.com",
    "password": "password"
}

// Response
{
    "user": {...},
    "token": "1|abc123...",
    "token_type": "Bearer"
}

// Headers para requests autenticados
Authorization: Bearer 1|abc123...
```

## 🛣️ Endpoints API

### Autenticación
```http
POST   /api/auth/login       # Login
POST   /api/auth/register    # Register  
POST   /api/auth/logout      # Logout
GET    /api/auth/user        # User actual
```

### Proyectos
```http
GET    /api/projects         # Listar proyectos
POST   /api/projects         # Crear proyecto
GET    /api/projects/{id}    # Ver proyecto
PUT    /api/projects/{id}    # Actualizar proyecto
DELETE /api/projects/{id}    # Eliminar proyecto
```

### Tareas
```http
GET    /api/projects/{id}/tasks     # Tareas del proyecto
POST   /api/projects/{id}/tasks     # Crear tarea
PUT    /api/tasks/{id}              # Actualizar tarea
DELETE /api/tasks/{id}              # Eliminar tarea
```

### Usuarios
```http
GET    /api/users            # Listar usuarios (admin)
GET    /api/users/search     # Buscar usuarios
```

## 🔧 Configuración

### Variables de Entorno
```bash
# .env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=mysql  # o localhost
DB_PORT=3306
DB_DATABASE=protasker
DB_USERNAME=protasker_user
DB_PASSWORD=protasker_password

SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost
```

## 🧪 Testing

### PHPUnit
```bash
php artisan test
php artisan test --filter AuthTest
php artisan test --coverage
```

## 🚀 Deployment

### Con Docker
```bash
# Con docker-compose
docker-compose up -d backend
```

### Optimización
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan optimize
```

---

**⬅️ [Volver al README principal](../README.md)**
