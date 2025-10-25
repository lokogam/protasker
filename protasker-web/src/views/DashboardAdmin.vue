<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useProjects } from '../composables/useProjects.js'
import { useRouter } from 'vue-router'
import ProjectCard from '../components/cards/ProjectCard.vue'
import ProjectForm from '../components/forms/ProjectForm.vue'
import AppButton from '../components/common/AppButton.vue'
import AppModal from '../components/common/AppModal.vue'

const { user } = useAuth()
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

// Computed para estadísticas generales
const stats = computed(() => {
  const allProjectsList = projects.value
  return {
    total: allProjectsList.length,
    pending: allProjectsList.filter(p => p.status === 'pending').length,
    in_progress: allProjectsList.filter(p => p.status === 'in_progress').length,
    completed: allProjectsList.filter(p => p.status === 'completed').length,
    cancelled: allProjectsList.filter(p => p.status === 'cancelled').length,
    users: [...new Set(allProjectsList.map(p => p.user_id))].length
  }
})

// Opciones de filtro por estado
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

// Cargar proyectos al montar el componente
onMounted(() => {
  fetchProjects()
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
          
          <AppButton
            variant="primary"
            @click="showCreateModal = true"
          >
            + Nuevo Proyecto
          </AppButton>
        </div>
      </div>

      <!-- Estadísticas Generales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-6">
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
              <p class="text-sm font-medium text-dark-600">Usuarios Activos</p>
              <p class="text-2xl font-bold text-info-600">{{ stats.users }}</p>
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
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
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

      <!-- Filtros y Búsqueda -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <!-- Búsqueda -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar proyectos, descripción o usuario..."
                class="w-full md:w-80 pl-10 pr-4 py-2 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                <svg class="h-5 w-5 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
            </div>

            <!-- Filtro por estado -->
            <select
              v-model="filterStatus"
              class="px-4 py-2 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>

          <div class="text-sm text-dark-500">
            Mostrando {{ allProjects.length }} de {{ stats.total }} proyectos
          </div>
        </div>
      </div>

      <!-- Lista de Proyectos -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
        <h2 class="text-xl font-semibold text-dark-900 mb-6">Todos los Proyectos</h2>
        
        <!-- Loading state -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>

        <!-- Empty state -->
        <div v-else-if="allProjects.length === 0 && !searchQuery && filterStatus === 'all'" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-dark-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-dark-900">No hay proyectos</h3>
          <p class="mt-1 text-sm text-dark-500">Comienza creando el primer proyecto de la plataforma.</p>
          <div class="mt-6">
            <AppButton
              variant="primary"
              @click="showCreateModal = true"
            >
              + Crear Proyecto
            </AppButton>
          </div>
        </div>

        <!-- No results state -->
        <div v-else-if="allProjects.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.347 0-4.518.893-6.14 2.36L12 21l6.14-3.64A7.962 7.962 0 0118 13.291zM6 9a6 6 0 1112 0v3a6 6 0 01-12 0V9z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-dark-900">No se encontraron proyectos</h3>
          <p class="mt-1 text-sm text-dark-500">
            Intenta ajustar los filtros o la búsqueda para encontrar lo que buscas.
          </p>
          <div class="mt-6">
            <AppButton
              variant="secondary"
              @click="searchQuery = ''; filterStatus = 'all'"
            >
              Limpiar Filtros
            </AppButton>
          </div>
        </div>

        <!-- Grid de proyectos -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ProjectCard
            v-for="project in allProjects"
            :key="project.id"
            :project="project"
            @edit="handleEditProject"
            @delete="handleDeleteProject"
            @view="handleViewProject"
          />
        </div>
      </div>
    </div>

    <!-- Modal para crear proyecto -->
    <AppModal
      v-model="showCreateModal"
      title="Crear Nuevo Proyecto"
      max-width="2xl"
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
      max-width="2xl"
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
      title="Confirmar Eliminación"
      max-width="md"
    >
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-danger-100 mb-4">
          <svg class="h-6 w-6 text-danger-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-dark-900 mb-2">¿Eliminar proyecto?</h3>
        <p class="text-sm text-dark-500 mb-6">
          ¿Estás seguro de que deseas eliminar el proyecto "{{ selectedProject?.name }}"? 
          Esta acción no se puede deshacer y eliminará todas las tareas asociadas.
        </p>
        <div class="flex justify-center space-x-3">
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