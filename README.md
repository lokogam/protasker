# 🚀 Protasker

**Sistema de Gestión de Proyectos y Tareas**

![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat&logo=vue.js&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)

## 📋 Descripción

Protasker es una aplicación web moderna para la gestión de proyectos y tareas, desarrollada con una arquitectura separada frontend/backend:

- **Frontend**: Vue.js 3 + Vite + Tailwind CSS
- **Backend**: Laravel 12 + Sanctum + Spatie Permission 
- **Base de datos**: MySQL 8.0
- **Contenedores**: Docker + Docker Compose

## ✨ Características Principales

### 🎯 **Gestión de Proyectos**
- Crear, editar y eliminar proyectos
- Cálculo automático de progreso basado en tareas
- Vista detallada con estadísticas

### 📝 **Gestión de Tareas**
- CRUD completo de tareas
- Estados: Pendiente, En Progreso, Completada
- Asignación de usuarios
- Contadores dinámicos por estado
- Filtros interactivos

### 👥 **Gestión de Usuarios**
- Sistema de roles (Administrador, Desarrollador)
- Autenticación con Laravel Sanctum
- Dashboards diferenciados por rol
- Búsqueda y autocompletado

### 🔐 **Seguridad**
- Autenticación basada en tokens
- Control de acceso por roles
- Middleware de autorización
- Validaciones frontend y backend

## 🏗️ Arquitectura

```
protasker/
├── protasker-api/          # Laravel 12 Backend API
│   ├── app/Models/         # Modelos (User, Project, Task)
│   ├── app/Http/Controllers/ # Controladores API
│   ├── database/migrations/ # Migraciones de BD
│   └── routes/api.php      # Rutas API
├── protasker-web/          # Vue.js 3 Frontend SPA
│   ├── src/components/     # Componentes Vue
│   ├── src/views/          # Vistas principales
│   ├── src/services/       # Servicios API
│   └── src/router/         # Enrutado
└── docker/                 # Configuración Docker
    ├── mysql/init/         # Scripts inicialización BD
    ├── php/local.ini       # Configuración PHP
    └── nginx/conf.d/       # Configuración Nginx
```

## 🐳 Instalación con Docker (RECOMENDADO)

> **📖 Para instalación completa, consulta:** [**DOCKER.md**](./DOCKER.md)

### ⚡ Inicio Rápido

```bash
# 1. Clonar repositorio
git clone <repo-url>
cd protasker

# 2. Configuración inicial (solo una vez)
./docker.sh setup          # Linux/Mac
# o
docker.bat setup           # Windows

# 3. Acceder a la aplicación
# Frontend: http://localhost:5173
# Backend:  http://localhost:8000
# MySQL:    localhost:3307
```

### 🔑 Credenciales por Defecto

- **Admin**: `admin@example.com` / `password`
- **Desarrollador**: `dev@example.com` / `password`

## 🛠️ Instalación Manual

### Requisitos
- PHP 8.2+
- Node.js 20+
- MySQL 8.0+
- Composer
- npm/yarn

### Backend (Laravel)
```bash
cd protasker-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Frontend (Vue.js)
```bash
cd protasker-web
npm install
npm run dev
```

## 🎮 Uso

### Dashboards por Rol

#### 👨‍💼 **Dashboard Administrador**
- Vista de todos los proyectos del sistema
- Gestión completa de usuarios y proyectos
- Estadísticas globales

#### 👨‍💻 **Dashboard Desarrollador**
- Vista de proyectos asignados únicamente
- Gestión de tareas propias
- Contadores de tareas por estado

### Flujo de Trabajo

1. **Crear Proyecto** (Admin)
2. **Asignar Desarrolladores** al proyecto
3. **Crear Tareas** dentro del proyecto
4. **Asignar Tareas** a desarrolladores
5. **Seguimiento de Progreso** automático

## 🔧 Comandos Útiles

### Docker
```bash
./docker.sh start      # Iniciar servicios
./docker.sh stop       # Detener servicios
./docker.sh logs       # Ver logs
./docker.sh status     # Ver estado
./docker.sh fresh      # Reset base de datos
```

### Desarrollo
```bash
# Backend
php artisan migrate:fresh --seed
php artisan tinker
composer test

# Frontend  
npm run dev
npm run build
npm run preview
```

## 📊 Stack Tecnológico

### Frontend
- **Vue.js 3**: Framework reactivo
- **Composition API**: Patrón de composición moderno
- **Vite**: Build tool ultra-rápido
- **Tailwind CSS**: Framework CSS utility-first
- **Vue Router**: Enrutado SPA
- **Pinia**: Estado global
- **Axios**: Cliente HTTP

### Backend
- **Laravel 12**: Framework PHP robusto
- **Sanctum**: Autenticación API
- **Spatie Permission**: Gestión de roles
- **Eloquent ORM**: Mapeo objeto-relacional
- **Form Requests**: Validaciones
- **API Resources**: Transformación de datos

### DevOps
- **Docker**: Contenedorización
- **Docker Compose**: Orquestación
- **MySQL**: Base de datos relacional
- **Nginx**: Servidor web (producción)

## 📁 Estructura de Base de Datos

```sql
users (id, name, email, password)
  ├─ hasMany → projects
  └─ hasMany → tasks (assigned_user_id)

projects (id, name, description, user_id, progress)
  ├─ belongsTo → user
  └─ hasMany → tasks

tasks (id, title, description, status, project_id, assigned_user_id)
  ├─ belongsTo → project  
  └─ belongsTo → user (assigned)
```

## 🤝 Contribución

1. Fork del repositorio
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver [LICENSE](LICENSE) para detalles.

## 👨‍💻 Desarrollado por

LinkedIn: [Duvan Gamboa](https://www.linkedin.com/in/duvan-gamboa-5193951b2/)  
Email: [duvangamboa8@gmail.com](mailto:duvangamboa8@gmail.com)
Web: [lokogam.github.io/Duvan-Gamboa](https://lokogam.github.io/Duvan-Gamboa/)

---

## 📚 Documentación Adicional

- [**🐳 Guía de Docker**](./DOCKER.md) - Instalación y uso con contenedores
- [**📖 API Documentation**](./protasker-api/README.md) - Endpoints y ejemplos
- [**🎨 Frontend Guide**](./protasker-web/README.md) - Componentes y arquitectura

---

<div align="center">

**¿Problemas con la instalación?** 

👉 **Consulta [DOCKER.md](./DOCKER.md) para instalación paso a paso** 👈

</div>