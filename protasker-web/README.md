# Vue 3 + Vite

This template should help get you started developing with Vue 3 in Vite. The template uses Vue 3 `<script setup>` SFCs, check out the [script setup docs](https://v3.vuejs.org/api/sfc-script-setup.html#sfc-script-setup) to learn more.

# 🎨 Protasker Frontend

**Vue.js 3 + Vite Frontend Application**

![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat&logo=vue.js&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-7.1-646CFF?style=flat&logo=vite&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind-4.1-38B2AC?style=flat&logo=tailwind-css&logoColor=white)

## 📋 Descripción

Frontend SPA (Single Page Application) de Protasker desarrollado con Vue.js 3, utilizando Composition API y las últimas tecnologías web.

## 🛠️ Stack Tecnológico

- **Vue.js 3.5.22**: Framework reactivo con Composition API
- **Vite 7.1.7**: Build tool ultra-rápido con HMR
- **Vue Router 4.4.5**: Enrutado SPA
- **Pinia 2.2.6**: Store para manejo de estado global  
- **Axios 1.7.7**: Cliente HTTP para API calls
- **Tailwind CSS 4.1.16**: Framework CSS utility-first
- **Node.js 20**: Runtime moderno con soporte ESM

## 🚀 Instalación

### Con Docker (Recomendado)
```bash
# Desde la raíz del proyecto
./docker.sh setup
```

### Manual
```bash
cd protasker-web
npm install
npm run dev
```

## 📜 Scripts Disponibles

```bash
npm run dev      # Servidor de desarrollo con HMR
npm run build    # Build para producción  
npm run preview  # Preview del build de producción
```

## 🏗️ Arquitectura

```
src/
├── components/          # Componentes reutilizables
│   ├── common/         # Componentes generales
│   ├── forms/          # Formularios
│   └── ui/             # Elementos UI básicos
├── views/              # Vistas principales (páginas)
│   ├── auth/           # Login, Register
│   ├── dashboard/      # Dashboards por rol
│   └── projects/       # Gestión de proyectos
├── services/           # Servicios API
│   ├── auth.js         # Autenticación
│   ├── projects.js     # API proyectos
│   └── tasks.js        # API tareas
├── stores/             # Pinia stores
│   ├── auth.js         # Estado autenticación
│   └── projects.js     # Estado proyectos
├── router/             # Configuración rutas
├── assets/             # Assets estáticos
└── main.js             # Punto de entrada
```

## 🎯 Características Principales

### 🔐 **Autenticación**
- Login/Register con validación
- Guards de rutas por rol
- Persistencia de sesión
- Logout automático por expiración

### 📊 **Dashboards Dinámicos**
- Dashboard Administrador: vista global
- Dashboard Desarrollador: vista personal
- Contadores en tiempo real
- Filtros interactivos

### 📝 **Gestión de Tareas**
- CRUD completo con validación
- Estados: Pendiente, En Progreso, Completada
- Asignación de usuarios con autocompletado
- Actualizaciones reactivas

### 🎨 **UI/UX**
- Diseño responsivo con Tailwind CSS
- Componentes reutilizables
- Loading states y feedback visual
- Animaciones suaves

## 🔧 Configuración

### Variables de Entorno

```bash
# .env o .env.docker
VITE_API_URL=http://localhost:8000/api
```

### Configuración de Desarrollo

```javascript
// vite.config.js
export default defineConfig({
  plugins: [
    vue(),
    tailwindcss()
  ],
  server: {
    host: '0.0.0.0',
    port: 5173
  }
})
```

## 🧩 Componentes Principales

### `<TaskForm>`
- Formulario para crear/editar tareas
- Validación en tiempo real
- Selector de usuarios con autocompletado

### `<ProjectCard>`
- Tarjeta de proyecto con progreso
- Contadores de tareas por estado
- Acciones rápidas

### `<TaskFilters>`
- Filtros clickeables por estado
- Búsqueda de tareas
- Reset de filtros

### `<UserSearch>`
- Autocompletado de usuarios
- Validación de selección
- Integración con API

## 🔄 Estado Global (Pinia)

### Auth Store
```javascript
// stores/auth.js
{
  user: null,
  token: null,
  isAuthenticated: false,
  login(),
  logout(),
  checkAuth()
}
```

### Projects Store
```javascript
// stores/projects.js
{
  projects: [],
  currentProject: null,
  tasks: [],
  filters: {},
  fetchProjects(),
  createTask(),
  updateTask()
}
```

## 🛣️ Rutas

```javascript
// router/index.js
/                    → Dashboard (protegido)
/login              → Login
/register           → Register
/projects/:id       → Detalle proyecto
/profile            → Perfil usuario
```

### Guards de Ruta
- `requireAuth`: Requiere autenticación
- `requireRole`: Requiere rol específico
- `redirectIfAuth`: Redirige si ya autenticado

## 🎨 Estilos

### Tailwind CSS
- Configuración personalizada en `tailwind.config.js`
- Componentes base en `style.css`
- Classes utilitarias para desarrollo rápido

### Convenciones
- Mobile-first responsive design
- Paleta de colores consistente
- Espaciado estandarizado (4px grid)
- Tipografía jerárquica

## 🔌 Integración con API

### Axios Setup
```javascript
// services/api.js
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

// Interceptors para auth token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})
```

### Error Handling
- Interceptors para manejo global de errores
- Notificaciones de error centralizadas
- Retry automático para requests fallidos

## 🧪 Testing

```bash
# Setup para testing (futuro)
npm install --save-dev @vue/test-utils vitest
npm run test
```

## 🚀 Deployment

### Build para Producción
```bash
npm run build
# Genera carpeta dist/ lista para servir
```

### Con Docker
```bash
# Perfil de producción con Nginx
docker-compose --profile production up -d
```

## 📱 Responsive Design

- **Mobile**: 320px - 768px
- **Tablet**: 768px - 1024px  
- **Desktop**: 1024px+

### Breakpoints Tailwind
```css
sm: '640px'   /* Small devices */
md: '768px'   /* Medium devices */
lg: '1024px'  /* Large devices */
xl: '1280px'  /* Extra large devices */
```

## 🔍 Debugging

### Vue DevTools
- Installar Vue DevTools browser extension
- Inspección de componentes y estado
- Time-travel debugging

### Vite HMR
- Hot Module Replacement automático
- Preservación de estado en desarrollo
- Error overlay integrado

## 📦 Dependencies

### Production
- `vue`: Framework core
- `vue-router`: SPA routing
- `pinia`: Estado global
- `axios`: HTTP client

### Development
- `@vitejs/plugin-vue`: Plugin Vue para Vite
- `tailwindcss`: Framework CSS
- `@tailwindcss/vite`: Plugin Tailwind para Vite
- `vite`: Build tool

## 🤝 Contribución

1. Seguir convenciones de Vue.js 3
2. Usar Composition API con `<script setup>`
3. Componentes en PascalCase
4. Props tipadas con TypeScript (futuro)
5. Tests unitarios para componentes críticos

---

## 📚 Recursos

- [Vue.js 3 Documentation](https://vuejs.org/)
- [Vite Documentation](https://vitejs.dev/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Vue Router](https://router.vuejs.org/)
- [Pinia](https://pinia.vuejs.org/)

---

**⬅️ [Volver al README principal](../README.md)**
