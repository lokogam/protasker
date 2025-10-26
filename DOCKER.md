# 🐳 Protasker Docker Setup

Este proyecto está configurado para ejecutarse completamente con Docker, incluyendo el frontend Vue.js, backend Laravel y base de datos MySQL.

## 📋 Requisitos Previos

- Docker Desktop instalado
- Docker Compose
- Git

## 🚀 Inicio Rápido

### 1. Clonar el repositorio
```bash
git clone <repo-url>
cd protasker
```

### 2. Configuración inicial (Una sola vez)
```bash
# En Linux/Mac
./docker.sh setup

# En Windows
docker.bat setup
```

### 3. Acceder a la aplicación
- **Frontend (Vue.js)**: http://localhost:5173
- **Backend API (Laravel)**: http://localhost:8000
- **Base de datos MySQL**: localhost:3307

## 🛠️ Comandos Disponibles

### Linux/Mac (docker.sh)
```bash
./docker.sh setup      # Configuración inicial
./docker.sh start      # Iniciar servicios
./docker.sh stop       # Detener servicios
./docker.sh restart    # Reiniciar servicios
./docker.sh logs       # Ver logs
./docker.sh backend    # Entrar al contenedor backend
./docker.sh frontend   # Entrar al contenedor frontend
./docker.sh mysql      # Entrar a MySQL
./docker.sh fresh      # Reset base de datos
./docker.sh build      # Reconstruir imágenes
./docker.sh clean      # Limpiar sistema
./docker.sh status     # Ver estado
```

### Windows (docker.bat)
```cmd
docker.bat setup      # Configuración inicial
docker.bat start      # Iniciar servicios
docker.bat stop       # Detener servicios
docker.bat restart    # Reiniciar servicios
docker.bat logs       # Ver logs
docker.bat backend    # Entrar al contenedor backend
docker.bat frontend   # Entrar al contenedor frontend
docker.bat mysql      # Entrar a MySQL
docker.bat fresh      # Reset base de datos
docker.bat build      # Reconstruir imágenes
docker.bat clean      # Limpiar sistema
docker.bat status     # Ver estado
```

## 🏗️ Arquitectura Docker

### Servicios

1. **MySQL (mysql)**
   - Puerto: 3307
   - Base de datos: protasker
   - Usuario: protasker_user
   - Contraseña: protasker_password

2. **Backend Laravel (backend)**
   - Puerto: 8000
   - PHP 8.2 + Laravel
   - Conecta a MySQL automáticamente

3. **Frontend Vue.js (frontend)**
   - Puerto: 5173
   - Node.js 20 + Vue 3 + Vite
   - Conecta al backend automáticamente

4. **Nginx (nginx)** - Solo para producción
   - Puerto: 80
   - Proxy para frontend y backend

### Volúmenes
- `mysql_data`: Datos persistentes de MySQL
- `./protasker-api`: Código backend (montado para desarrollo)
- `./protasker-web`: Código frontend (montado para desarrollo)

## 🔧 Desarrollo

### Hot Reload
Ambos servicios (frontend y backend) están configurados para recarga automática:
- Los cambios en `protasker-web/` se reflejan automáticamente
- Los cambios en `protasker-api/` se reflejan automáticamente

### Base de datos
La base de datos se inicializa automáticamente con:
- Migraciones de Laravel
- Seeders con datos de prueba
- Usuarios de prueba:
  - Admin: admin@protasker.com / password
  - Dev: dev@protasker.com / password

### Logs
Ver logs en tiempo real:
```bash
./docker.sh logs
```

### Comandos dentro de contenedores

#### Backend (Laravel)
```bash
./docker.sh backend
# Dentro del contenedor:
php artisan migrate
php artisan db:seed
php artisan tinker
composer install
```

#### Frontend (Vue.js)
```bash
./docker.sh frontend
# Dentro del contenedor:
npm install
npm run build
npm run test
```

#### MySQL
```bash
./docker.sh mysql
# Acceso directo a MySQL CLI
```

## 🚧 Troubleshooting

### Puerto ocupado
Si algún puerto está ocupado, modificar en `docker-compose.yml`:
```yaml
ports:
  - "8001:8000"  # Cambiar puerto local
```

### Permisos en Linux/Mac
```bash
chmod +x docker.sh
```

### Limpiar y reiniciar
```bash
./docker.sh clean
./docker.sh setup
```

### Ver estado de contenedores
```bash
./docker.sh status
docker ps
```

## 📁 Estructura de Archivos Docker

```
protasker/
├── docker-compose.yml          # Configuración principal
├── docker.sh                   # Script Linux/Mac
├── docker.bat                  # Script Windows
├── docker/                     # Configuraciones Docker
│   ├── mysql/init/             # Scripts inicialización MySQL
│   ├── php/local.ini           # Configuración PHP
│   └── nginx/conf.d/           # Configuración Nginx
├── protasker-api/
│   ├── Dockerfile              # Imagen backend
│   └── .env.docker             # Variables entorno backend
└── protasker-web/
    ├── Dockerfile              # Imagen frontend
    └── .env.docker             # Variables entorno frontend
```

## 🌐 URLs de Desarrollo

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **MySQL**: localhost:3307

## 🔒 Credenciales por Defecto

### Base de datos
- Host: localhost (o mysql desde contenedores)
- Puerto: 3307
- Base de datos: protasker
- Usuario: protasker_user
- Contraseña: protasker_password

### Usuarios de aplicación
- **Admin**: admin@protasker.com / password
- **Desarrollador**: dev@protasker.com / password

## 📝 Notas Importantes

1. **Primera ejecución**: El setup inicial puede tomar varios minutos
2. **Datos persistentes**: Los datos de MySQL se mantienen entre reinicios
3. **Hot reload**: Ambos servicios se recargan automáticamente al cambiar código
4. **Red interna**: Los contenedores se comunican por nombres de servicio
5. **Desarrollo**: Todos los archivos están montados para desarrollo activo