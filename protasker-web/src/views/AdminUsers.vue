<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useUsers } from '../composables/useUsers.js'
import { useProjects } from '../composables/useProjects.js'
import { useRouter } from 'vue-router'
import AppButton from '../components/common/AppButton.vue'

const { user, logout } = useAuth()
const router = useRouter()
const {
  users,
  developers,
  fetchUsers,
  fetchDevelopers
} = useUsers()

const {
  projects,
  fetchProjects
} = useProjects()

// Estados del componente
const searchQuery = ref('')
const filterRole = ref('all')

// Opciones de filtro
const roleOptions = [
  { value: 'all', label: 'Todos los Roles' },
  { value: 'administrador', label: 'Administradores' },
  { value: 'desarrollador', label: 'Desarrolladores' }
]

// Computed para filtrar usuarios
const filteredUsers = computed(() => {
  let filtered = users.value

  // Filtrar por rol
  if (filterRole.value !== 'all') {
    filtered = filtered.filter(user => 
      user.roles?.some(role => role.name === filterRole.value)
    )
  }

  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(user => 
      user.name.toLowerCase().includes(query) ||
      user.email.toLowerCase().includes(query)
    )
  }

  return filtered
})

// Computed para estadísticas
const stats = computed(() => {
  return {
    total: users.value.length,
    admins: users.value.filter(u => u.roles?.some(r => r.name === 'administrador')).length,
    developers: users.value.filter(u => u.roles?.some(r => r.name === 'desarrollador')).length
  }
})

// Función para obtener el rol principal del usuario
const getUserRole = (user) => {
  return user.roles?.[0]?.name || 'Sin rol'
}

// Función para obtener el color del rol
const getRoleColor = (roleName) => {
  switch (roleName) {
    case 'administrador':
      return 'bg-danger-100 text-danger-800'
    case 'desarrollador':
      return 'bg-primary-100 text-primary-800'
    default:
      return 'bg-dark-100 text-dark-800'
  }
}

// Función para contar proyectos del usuario
const getUserProjectsCount = (userId) => {
  return projects.value.filter(p => p.user?.id === userId).length
}

// Manejar cierre de sesión
const handleLogout = async () => {
  try {
    await logout()
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}

// Navegar a vista de proyectos
const goToProjects = () => {
  router.push('/admin/projects')
}

// Navegar al dashboard principal
const goToDashboard = () => {
  router.push('/dashboard/admin')
}

// Cargar datos al montar el componente
onMounted(async () => {
  await fetchUsers()
  await fetchProjects()
})
</script>

<template>
  <div class="min-h-screen bg-dark-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6 mb-6">
        <div class="flex justify-between items-center">
          <div class="flex items-center space-x-4">
            <button 
              @click="goToDashboard"
              class="flex items-center text-dark-600 hover:text-dark-900 transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Dashboard
            </button>
            
            <div class="border-l border-dark-300 pl-4">
              <h1 class="text-3xl font-bold text-dark-900">Gestión de Usuarios</h1>
              <p class="text-dark-600 mt-2">Administración de todos los usuarios del sistema</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <AppButton
              variant="secondary"
              @click="goToProjects"
            >
              Ver Proyectos
            </AppButton>
            
            <AppButton
              variant="secondary"
              @click="handleLogout"
            >
              Cerrar Sesión
            </AppButton>
          </div>
        </div>
      </div>

      <!-- Estadísticas de Usuarios -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Total Usuarios</p>
              <p class="text-2xl font-bold text-dark-900">{{ stats.total }}</p>
            </div>
            <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-info-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Administradores</p>
              <p class="text-2xl font-bold text-danger-600">{{ stats.admins }}</p>
            </div>
            <div class="w-12 h-12 bg-danger-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-danger-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Desarrolladores</p>
              <p class="text-2xl font-bold text-primary-600">{{ stats.developers }}</p>
            </div>
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <!-- Filtro por rol -->
            <div>
              <label class="block text-sm font-medium text-dark-700 mb-2">Rol</label>
              <select 
                v-model="filterRole"
                class="px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
                <option v-for="option in roleOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>

            <!-- Búsqueda -->
            <div>
              <label class="block text-sm font-medium text-dark-700 mb-2">Buscar</label>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar usuarios..."
                class="px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              />
            </div>
          </div>

          <div class="text-sm text-dark-600">
            Mostrando {{ filteredUsers.length }} de {{ users.length }} usuarios
          </div>
        </div>
      </div>

      <!-- Lista de Usuarios -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
        <h2 class="text-xl font-semibold text-dark-900 mb-6">Todos los Usuarios</h2>
        
        <div v-if="filteredUsers.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.25"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-dark-900">No hay usuarios</h3>
          <p class="mt-1 text-sm text-dark-500">No se encontraron usuarios con los criterios seleccionados.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="userItem in filteredUsers"
            :key="userItem.id"
            class="bg-white border border-dark-200 rounded-lg p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex items-center space-x-4 mb-4">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-lg font-semibold text-dark-900 truncate">{{ userItem.name }}</p>
                <p class="text-sm text-dark-600 truncate">{{ userItem.email }}</p>
              </div>
            </div>
            
            <!-- Rol del usuario -->
            <div class="mb-4">
              <span 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getRoleColor(getUserRole(userItem))"
              >
                {{ getUserRole(userItem) === 'administrador' ? 'Administrador' : 
                   getUserRole(userItem) === 'desarrollador' ? 'Desarrollador' : 'Sin rol' }}
              </span>
            </div>
            
            <!-- Estadísticas del usuario -->
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div class="text-center">
                <p class="text-dark-500">Proyectos</p>
                <p class="text-lg font-semibold text-dark-900">
                  {{ getUserProjectsCount(userItem.id) }}
                </p>
              </div>
              <div class="text-center">
                <p class="text-dark-500">Registro</p>
                <p class="text-sm text-dark-600">
                  {{ new Date(userItem.created_at).toLocaleDateString() }}
                </p>
              </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-4 pt-4 border-t border-dark-200">
              <div class="flex justify-between items-center text-sm">
                <span class="text-dark-500">Estado:</span>
                <span class="text-secondary-600 font-medium">Activo</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>