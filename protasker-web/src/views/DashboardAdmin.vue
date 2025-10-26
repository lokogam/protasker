<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useProjects } from '../composables/useProjects.js'
import { useUsers } from '../composables/useUsers.js'
import { useRouter } from 'vue-router'
import AppButton from '../components/common/AppButton.vue'

const { user, logout } = useAuth()
const router = useRouter()
const { 
  projects, 
  fetchProjects
} = useProjects()

const {
  users,
  fetchUsers
} = useUsers()

// Computed para estadísticas generales
const projectStats = computed(() => {
  return {
    total: projects.value.length,
    pending: projects.value.filter(p => p.status === 'pending').length,
    in_progress: projects.value.filter(p => p.status === 'in_progress').length,
    completed: projects.value.filter(p => p.status === 'completed').length,
    cancelled: projects.value.filter(p => p.status === 'cancelled').length
  }
})

const userStats = computed(() => {
  return {
    total: users.value.length,
    admins: users.value.filter(u => u.roles?.some(r => r.name === 'administrador')).length,
    developers: users.value.filter(u => u.roles?.some(r => r.name === 'desarrollador')).length
  }
})

// Manejar cierre de sesión
const handleLogout = async () => {
  try {
    await logout()
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}

// Navegación
const goToProjects = () => {
  router.push('/admin/projects')
}

const goToUsers = () => {
  router.push('/admin/users')
}

const goToAllTasks = () => {
  router.push('/tasks/all')
}

// Cargar datos al montar el componente
onMounted(async () => {
  await fetchProjects()
  await fetchUsers()
})
</script>

<template>
  <div class="min-h-screen bg-dark-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6 mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-dark-900">Dashboard Administrador</h1>
            <p class="text-dark-600 mt-2">Panel de control general - {{ user?.name }}</p>
          </div>
          
          <div class="flex items-center space-x-3">            
            <AppButton
              variant="secondary"
              @click="handleLogout"
            >
              Cerrar Sesión
            </AppButton>
          </div>
        </div>
      </div>

      <!-- Resumen General -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Resumen de Proyectos -->
        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-dark-900">Proyectos</h2>
            <AppButton
              variant="primary"
              @click="goToProjects"
            >
              Gestionar Proyectos
            </AppButton>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-dark-50 rounded-lg">
              <p class="text-2xl font-bold text-dark-900">{{ projectStats.total }}</p>
              <p class="text-sm text-dark-600">Total</p>
            </div>
            <div class="text-center p-4 bg-warning-50 rounded-lg">
              <p class="text-2xl font-bold text-warning-600">{{ projectStats.pending }}</p>
              <p class="text-sm text-dark-600">Pendientes</p>
            </div>
            <div class="text-center p-4 bg-primary-50 rounded-lg">
              <p class="text-2xl font-bold text-primary-600">{{ projectStats.in_progress }}</p>
              <p class="text-sm text-dark-600">En Progreso</p>
            </div>
            <div class="text-center p-4 bg-secondary-50 rounded-lg">
              <p class="text-2xl font-bold text-secondary-600">{{ projectStats.completed }}</p>
              <p class="text-sm text-dark-600">Completados</p>
            </div>
          </div>
        </div>

        <!-- Resumen de Usuarios -->
        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-dark-900">Usuarios</h2>
            <AppButton
              variant="primary"
              @click="goToUsers"
            >
              Gestionar Usuarios
            </AppButton>
          </div>
          
          <div class="grid grid-cols-1 gap-4">
            <div class="text-center p-4 bg-info-50 rounded-lg">
              <p class="text-2xl font-bold text-info-600">{{ userStats.total }}</p>
              <p class="text-sm text-dark-600">Total Usuarios</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div class="text-center p-4 bg-danger-50 rounded-lg">
                <p class="text-2xl font-bold text-danger-600">{{ userStats.admins }}</p>
                <p class="text-sm text-dark-600">Administradores</p>
              </div>
              <div class="text-center p-4 bg-primary-50 rounded-lg">
                <p class="text-2xl font-bold text-primary-600">{{ userStats.developers }}</p>
                <p class="text-sm text-dark-600">Desarrolladores</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Acciones Rápidas -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
        <h2 class="text-xl font-semibold text-dark-900 mb-6">Acciones Rápidas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Gestionar Proyectos -->
          <div 
            @click="goToProjects"
            class="cursor-pointer group bg-primary-50 hover:bg-primary-100 rounded-lg p-6 transition-colors"
          >
            <div class="flex items-center justify-center w-12 h-12 bg-primary-100 group-hover:bg-primary-200 rounded-lg mb-4">
              <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-dark-900 mb-2">Gestionar Proyectos</h3>
            <p class="text-sm text-dark-600">Ver, crear y administrar todos los proyectos del sistema</p>
          </div>

          <!-- Gestionar Usuarios -->
          <div 
            @click="goToUsers"
            class="cursor-pointer group bg-info-50 hover:bg-info-100 rounded-lg p-6 transition-colors"
          >
            <div class="flex items-center justify-center w-12 h-12 bg-info-100 group-hover:bg-info-200 rounded-lg mb-4">
              <svg class="w-6 h-6 text-info-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-dark-900 mb-2">Gestionar Usuarios</h3>
            <p class="text-sm text-dark-600">Ver y administrar todos los usuarios del sistema</p>
          </div>

          <!-- Ver Todas las Tareas -->
          <div 
            @click="goToAllTasks"
            class="cursor-pointer group bg-warning-50 hover:bg-warning-100 rounded-lg p-6 transition-colors"
          >
            <div class="flex items-center justify-center w-12 h-12 bg-warning-100 group-hover:bg-warning-200 rounded-lg mb-4">
              <svg class="w-6 h-6 text-warning-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-dark-900 mb-2">Todas las Tareas</h3>
            <p class="text-sm text-dark-600">Ver y gestionar todas las tareas del sistema</p>
          </div>

          
        </div>
      </div>
    </div>
  </div>
</template>