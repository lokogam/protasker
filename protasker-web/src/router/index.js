import { createRouter, createWebHistory } from 'vue-router'
import { authService } from '../services/auth.js'

// Importar vistas
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import DashboardDev from '../views/DashboardDev.vue'
import DashboardAdmin from '../views/DashboardAdmin.vue'
import AdminProjects from '../views/AdminProjects.vue'
import AdminUsers from '../views/AdminUsers.vue'
import AllTasks from '../views/AllTasks.vue'
import ProjectTasks from '../views/ProjectTasks.vue'

// Definir rutas
const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/dashboard/developer',
    name: 'DashboardDev',
    component: DashboardDev,
    meta: { 
      requiresAuth: true,
      requiresRole: 'desarrollador'
    }
  },
  {
    path: '/dashboard/admin',
    name: 'DashboardAdmin',
    component: DashboardAdmin,
    meta: { 
      requiresAuth: true,
      requiresRole: 'administrador'
    }
  },
  {
    path: '/admin/projects',
    name: 'AdminProjects',
    component: AdminProjects,
    meta: { 
      requiresAuth: true,
      requiresRole: 'administrador'
    }
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
    meta: { 
      requiresAuth: true,
      requiresRole: 'administrador'
    }
  },
  {
    path: '/tasks/all',
    name: 'AllTasks',
    component: AllTasks,
    meta: { 
      requiresAuth: true
    }
  },
  {
    path: '/projects/:id/tasks',
    name: 'ProjectTasks',
    component: ProjectTasks,
    meta: { 
      requiresAuth: true
    }
  }
]

// Crear router
const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guard de navegación global
router.beforeEach(async (to, from, next) => {
  const isAuthenticated = authService.isAuthenticated()

  // Si la ruta requiere autenticación y no está autenticado
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
    return
  }

  // Si la ruta requiere ser invitado (como login) y ya está autenticado
  if (to.meta.requiresGuest && isAuthenticated) {
    // Obtener usuario para redireccionar según su rol
    try {
      const userData = await authService.getUser()
      if (userData?.roles?.[0]?.name === 'administrador') {
        next('/dashboard/admin')
      } else {
        next('/dashboard/developer')
      }
    } catch (error) {
      next('/dashboard')
    }
    return
  }

  // Verificar roles específicos
  if (to.meta.requiresRole && isAuthenticated) {
    try {
      const userData = await authService.getUser()
      const userRole = userData?.roles?.[0]?.name
      
      if (to.meta.requiresRole !== userRole) {
        // Redireccionar al dashboard correcto según el rol
        if (userRole === 'administrador') {
          next('/dashboard/admin')
        } else {
          next('/dashboard/developer')
        }
        return
      }
    } catch (error) {
      console.error('Error verifying user role:', error)
      next('/login')
      return
    }
  }

  // Permitir navegación
  next()
})

export default router