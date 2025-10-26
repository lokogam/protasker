<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useProjects } from '../composables/useProjects.js'
import { useRouter } from 'vue-router'
import ProjectCard from '../components/cards/ProjectCard.vue'
import ProjectForm from '../components/forms/ProjectForm.vue'
import AppButton from '../components/common/AppButton.vue'
import AppModal from '../components/common/AppModal.vue'

const { user, logout } = useAuth()
const router = useRouter()
const { 
  projects, 
  loading, 
  fetchProjects, 
  createProject, 
  updateProject, 
  deleteProject 
} = useProjects()

// Estados del componente
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedProject = ref(null)
const formLoading = ref(false)
const filterStatus = ref('all')
const searchQuery = ref('')

// Computed para todos los proyectos (administrador ve todos)
const allProjects = computed(() => {
  let filtered = projects.value

  // Filtrar por estado
  if (filterStatus.value !== 'all') {
    filtered = filtered.filter(project => project.status === filterStatus.value)
  }

  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(project => 
      project.name.toLowerCase().includes(query) ||
      project.description?.toLowerCase().includes(query) ||
      project.user?.name.toLowerCase().includes(query)
    )
  }

  return filtered
})

// Computed para estadísticas
const stats = computed(() => {
  return {
    total: projects.value.length,
    pending: projects.value.filter(p => p.status === 'pending').length,
    in_progress: projects.value.filter(p => p.status === 'in_progress').length,
    completed: projects.value.filter(p => p.status === 'completed').length,
    cancelled: projects.value.filter(p => p.status === 'cancelled').length
  }
})

// Opciones de filtro
const statusOptions = [
  { value: 'all', label: 'Todos los Estados' },
  { value: 'pending', label: 'Pendientes' },
  { value: 'in_progress', label: 'En Progreso' },
  { value: 'completed', label: 'Completados' },
  { value: 'cancelled', label: 'Cancelados' }
]

// Funciones para manejar proyectos
const handleCreateProject = async (projectData) => {
  formLoading.value = true
  try {
    await createProject(projectData)
    showCreateModal.value = false
    await fetchProjects() // Refrescar lista
  } catch (error) {
    console.error('Error creating project:', error)
  } finally {
    formLoading.value = false
  }
}

const handleEditProject = (project) => {
  selectedProject.value = project
  showEditModal.value = true
}

const handleUpdateProject = async (projectData) => {
  formLoading.value = true
  try {
    await updateProject(selectedProject.value.id, projectData)
    showEditModal.value = false
    selectedProject.value = null
    await fetchProjects() // Refrescar lista
  } catch (error) {
    console.error('Error updating project:', error)
  } finally {
    formLoading.value = false
  }
}

const handleDeleteProject = (project) => {
  selectedProject.value = project
  showDeleteModal.value = true
}

const confirmDeleteProject = async () => {
  try {
    await deleteProject(selectedProject.value.id)
    showDeleteModal.value = false
    selectedProject.value = null
    await fetchProjects() // Refrescar lista
  } catch (error) {
    console.error('Error deleting project:', error)
  }
}

const handleViewProject = (project) => {
  // Navegar a la vista de tareas del proyecto
  router.push(`/projects/${project.id}/tasks`)
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

// Navegar a vista de usuarios
const goToUsers = () => {
  router.push('/admin/users')
}

// Navegar al dashboard principal
const goToDashboard = () => {
  router.push('/dashboard/admin')
}

// Manejar filtros clickeables desde ProjectCard
const handleTaskFilter = ({ projectId, status }) => {
  // Navegar a la vista de tareas del proyecto con filtro aplicado
  const params = status !== 'all' ? `?status=${status}` : ''
  router.push(`/projects/${projectId}/tasks${params}`)
}

// Cargar proyectos al montar el componente
onMounted(async () => {
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
              <h1 class="text-3xl font-bold text-dark-900">Gestión de Proyectos</h1>
              <p class="text-dark-600 mt-2">Administración de todos los proyectos</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <AppButton
              variant="secondary"
              @click="goToUsers"
            >
              Ver Usuarios
            </AppButton>
            
            <AppButton
              variant="primary"
              @click="showCreateModal = true"
            >
              + Nuevo Proyecto
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

      <!-- Estadísticas Generales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Total Proyectos</p>
              <p class="text-2xl font-bold text-dark-900">{{ stats.total }}</p>
            </div>
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Pendientes</p>
              <p class="text-2xl font-bold text-warning-600">{{ stats.pending }}</p>
            </div>
            <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-warning-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">En Progreso</p>
              <p class="text-2xl font-bold text-primary-600">{{ stats.in_progress }}</p>
            </div>
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 8.707 7.621a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Completados</p>
              <p class="text-2xl font-bold text-secondary-600">{{ stats.completed }}</p>
            </div>
            <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-secondary-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Cancelados</p>
              <p class="text-2xl font-bold text-danger-600">{{ stats.cancelled }}</p>
            </div>
            <div class="w-12 h-12 bg-danger-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-danger-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <!-- Filtro por estado -->
            <div>
              <label class="block text-sm font-medium text-dark-700 mb-2">Estado</label>
              <select 
                v-model="filterStatus"
                class="px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
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
                placeholder="Buscar proyectos..."
                class="px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              />
            </div>
          </div>

          <div class="text-sm text-dark-600">
            Mostrando {{ allProjects.length }} de {{ projects.length }} proyectos
          </div>
        </div>
      </div>

      <!-- Lista de Proyectos -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
        <h2 class="text-xl font-semibold text-dark-900 mb-6">Todos los Proyectos</h2>
        
        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
          <p class="text-dark-600 mt-4">Cargando proyectos...</p>
        </div>

        <div v-else-if="allProjects.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-dark-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-dark-900">No hay proyectos</h3>
          <p class="mt-1 text-sm text-dark-500">Comienza creando un nuevo proyecto.</p>
          <div class="mt-6">
            <AppButton variant="primary" @click="showCreateModal = true">
              + Nuevo Proyecto
            </AppButton>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ProjectCard
            v-for="project in allProjects"
            :key="project.id"
            :project="project"
            @edit="handleEditProject"
            @delete="handleDeleteProject"
            @view="handleViewProject"
            @filter-tasks="handleTaskFilter"
          />
        </div>
      </div>
    </div>

    <!-- Modal para crear proyecto -->
    <AppModal
      v-model="showCreateModal"
      title="Crear Nuevo Proyecto"
      :maxWidth="'2xl'"
    >
      <ProjectForm
        :is-loading="formLoading"
        @submit="handleCreateProject"
        @cancel="showCreateModal = false"
      />
    </AppModal>

    <!-- Modal para editar proyecto -->
    <AppModal
      v-model="showEditModal"
      title="Editar Proyecto"
      :maxWidth="'2xl'"
    >
      <ProjectForm
        :project="selectedProject"
        :is-loading="formLoading"
        @submit="handleUpdateProject"
        @cancel="showEditModal = false"
      />
    </AppModal>

    <!-- Modal de confirmación para eliminar -->
    <AppModal
      v-model="showDeleteModal"
      title="Eliminar Proyecto"
      :maxWidth="'md'"
    >
      <div class="space-y-4">
        <p class="text-dark-700">
          ¿Estás seguro de que quieres eliminar el proyecto <strong>{{ selectedProject?.name }}</strong>?
        </p>
        <p class="text-sm text-danger-600">
          Esta acción no se puede deshacer.
        </p>
        
        <div class="flex justify-end space-x-3 pt-4">
          <AppButton
            variant="secondary"
            @click="showDeleteModal = false"
          >
            Cancelar
          </AppButton>
          <AppButton
            variant="danger"
            @click="confirmDeleteProject"
          >
            Eliminar
          </AppButton>
        </div>
      </div>
    </AppModal>
  </div>
</template>