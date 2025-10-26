<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTasks } from '../composables/useTasks.js'
import { useProjects } from '../composables/useProjects.js'
import { useAuth } from '../composables/useAuth.js'
import TaskCard from '../components/cards/TaskCard.vue'
import TaskForm from '../components/forms/TaskForm.vue'
import ProgressBar from '../components/common/ProgressBar.vue'
import AppButton from '../components/common/AppButton.vue'
import AppModal from '../components/common/AppModal.vue'

const route = useRoute()
const router = useRouter()
const { user, logout } = useAuth()
const { 
  tasks, 
  loading, 
  taskStats,
  fetchProjectTasks, 
  createTask, 
  updateTask, 
  deleteTask,
  clearTasks
} = useTasks()
const { fetchProject } = useProjects()

// Estado del componente
const project = ref(null)
const projectLoading = ref(false)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)
const selectedTask = ref(null)
const formLoading = ref(false)
const filterStatus = ref('all')
const searchQuery = ref('')

// Obtener ID del proyecto desde la ruta
const projectId = computed(() => route.params.id)

// Tareas filtradas
const filteredTasks = computed(() => {
  let filtered = tasks.value

  // Filtrar por estado
  if (filterStatus.value !== 'all') {
    filtered = filtered.filter(task => task.status === filterStatus.value)
  }

  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(task => 
      task.name.toLowerCase().includes(query) ||
      task.description?.toLowerCase().includes(query) ||
      task.assigned_user?.name.toLowerCase().includes(query)
    )
  }

  return filtered
})

// Opciones de filtro
const statusOptions = [
  { value: 'all', label: 'Todos los Estados' },
  { value: 'pending', label: 'Pendientes' },
  { value: 'in_progress', label: 'En Progreso' },
  { value: 'completed', label: 'Completadas' }
]

// Cargar datos del proyecto y tareas
const loadProjectData = async () => {
  projectLoading.value = true
  try {
    // Cargar información del proyecto
    const projectData = await fetchProject(projectId.value)
    if (projectData) {
      project.value = projectData
    }

    // Cargar tareas del proyecto
    await fetchProjectTasks(projectId.value)
  } catch (error) {
    console.error('Error loading project data:', error)
    // Si hay error, redirigir al dashboard
    router.push('/dashboard')
  } finally {
    projectLoading.value = false
  }
}

// Funciones para manejar tareas
const handleCreateTask = async (taskData) => {
  formLoading.value = true
  try {
    await createTask({ ...taskData, project_id: projectId.value })
    showCreateModal.value = false
    await loadProjectData() // Refrescar datos para actualizar progreso del proyecto
  } catch (error) {
    console.error('Error creating task:', error)
  } finally {
    formLoading.value = false
  }
}

const handleEditTask = (task) => {
  selectedTask.value = task
  showEditModal.value = true
}

const handleUpdateTask = async (taskData) => {
  formLoading.value = true
  console.log('Updating task with data:', taskData)
  console.log('Selected task ID:', selectedTask.value?.id)
  
  try {
    const result = await updateTask(selectedTask.value.id, taskData)
    console.log('Update result:', result)
    showEditModal.value = false
    selectedTask.value = null
    await loadProjectData() // Refrescar datos
  } catch (error) {
    console.error('Error updating task:', error)
    // Mostrar error al usuario
    alert('Error al actualizar la tarea: ' + error.message)
  } finally {
    formLoading.value = false
  }
}

const handleDeleteTask = (task) => {
  selectedTask.value = task
  showDeleteModal.value = true
}

const confirmDeleteTask = async () => {
  try {
    await deleteTask(selectedTask.value.id)
    showDeleteModal.value = false
    selectedTask.value = null
    await loadProjectData() // Refrescar datos
  } catch (error) {
    console.error('Error deleting task:', error)
  }
}

const handleViewTask = (task) => {
  selectedTask.value = task
  showViewModal.value = true
}

const handleAssignTask = (task) => {
  // Abrir modal de edición con foco en asignación
  selectedTask.value = task
  showEditModal.value = true
}

// Volver al dashboard del usuario
const goBack = () => {
  if (user.value?.roles?.[0]?.name === 'administrador') {
    router.push('/dashboard/admin')
  } else {
    router.push('/dashboard/developer')
  }
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

// Cargar datos al montar el componente
onMounted(() => {
  clearTasks() // Limpiar tareas previas
  
  // Aplicar filtro desde URL si existe
  if (route.query.status && route.query.status !== 'all') {
    filterStatus.value = route.query.status
  }
  
  loadProjectData()
})
</script>

<template>
  <div class="min-h-screen bg-dark-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Loading del proyecto -->
      <div v-if="projectLoading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <!-- Contenido principal -->
      <div v-else>
        <!-- Header del proyecto -->
        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6 mb-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <button 
                @click="goBack"
                class="flex items-center text-dark-600 hover:text-dark-900 transition-colors"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver
              </button>
              
              <div class="border-l border-dark-300 pl-4">
                <h1 class="text-3xl font-bold text-dark-900">{{ project?.name || 'Cargando...' }}</h1>
                <p v-if="project?.description" class="text-dark-600 mt-1">{{ project.description }}</p>
                <div class="flex items-center space-x-4 mt-2">
                  <span class="text-sm text-dark-500">
                    Progreso: <strong class="text-primary-600">{{ project?.progress || 0 }}%</strong>
                  </span>
                  <span class="text-sm text-dark-500">
                    Creado por: <strong>{{ project?.user?.name || 'N/A' }}</strong>
                  </span>
                </div>
              </div>
            </div>
            
            <div class="flex items-center space-x-3">
              <AppButton
                variant="primary"
                @click="showCreateModal = true"
              >
                + Nueva Tarea
              </AppButton>
              
              <AppButton
                variant="secondary"
                @click="handleLogout"
              >
                Cerrar Sesión
              </AppButton>
            </div>
          </div>

          <!-- Barra de progreso del proyecto -->
          <div v-if="project" class="mt-4">
            <div class="w-full bg-dark-200 rounded-full h-3">
              <ProgressBar :progress="Number(project.progress)" size="md" />
            </div>
          </div>
        </div>

        <!-- Estadísticas de tareas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
            <div class="flex items-center">
              <div class="flex-1">
                <p class="text-sm font-medium text-dark-600">Total Tareas</p>
                <p class="text-2xl font-bold text-dark-900">{{ taskStats.total }}</p>
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
                <p class="text-2xl font-bold text-warning-600">{{ taskStats.pending }}</p>
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
                <p class="text-2xl font-bold text-primary-600">{{ taskStats.in_progress }}</p>
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
                <p class="text-sm font-medium text-dark-600">Completadas</p>
                <p class="text-2xl font-bold text-secondary-600">{{ taskStats.completed }}</p>
              </div>
              <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-secondary-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
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
                  placeholder="Buscar tareas o usuario asignado..."
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
              Mostrando {{ filteredTasks.length }} de {{ taskStats.total }} tareas
            </div>
          </div>
        </div>

        <!-- Lista de Tareas -->
        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <h2 class="text-xl font-semibold text-dark-900 mb-6">Tareas del Proyecto</h2>
          
          <!-- Loading state -->
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
          </div>

          <!-- Empty state -->
          <div v-else-if="filteredTasks.length === 0 && !searchQuery && filterStatus === 'all'" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-dark-900">No hay tareas</h3>
            <p class="mt-1 text-sm text-dark-500">Comienza creando la primera tarea para este proyecto.</p>
            <div class="mt-6">
              <AppButton
                variant="primary"
                @click="showCreateModal = true"
              >
                + Crear Tarea
              </AppButton>
            </div>
          </div>

          <!-- No results state -->
          <div v-else-if="filteredTasks.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.347 0-4.518.893-6.14 2.36L12 21l6.14-3.64A7.962 7.962 0 0118 13.291zM6 9a6 6 0 1112 0v3a6 6 0 01-12 0V9z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-dark-900">No se encontraron tareas</h3>
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

          <!-- Grid de tareas -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <TaskCard
              v-for="task in filteredTasks"
              :key="task.id"
              :task="task"
              @edit="handleEditTask"
              @delete="handleDeleteTask"
              @view="handleViewTask"
              @assign="handleAssignTask"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para crear tarea -->
    <AppModal
      v-model="showCreateModal"
      title="Crear Nueva Tarea"
      :maxWidth="'2xl'"
    >
      <TaskForm
        :project-id="projectId"
        :is-loading="formLoading"
        @submit="handleCreateTask"
        @cancel="showCreateModal = false"
      />
    </AppModal>

    <!-- Modal para editar tarea -->
    <AppModal
      v-model="showEditModal"
      title="Editar Tarea"
      :maxWidth="'2xl'"
    >
      <TaskForm
        :task="selectedTask"
        :is-loading="formLoading"
        @submit="handleUpdateTask"
        @cancel="showEditModal = false"
      />
    </AppModal>

    <!-- Modal para ver detalles de tarea -->
    <AppModal
      v-model="showViewModal"
      title="Detalles de la Tarea"
      :maxWidth="'2xl'"
    >
      <div v-if="selectedTask" class="space-y-6">
        <!-- Encabezado con título y estado -->
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-xl font-semibold text-dark-900">{{ selectedTask.name }}</h3>
            <div class="mt-2">
              <span 
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="{
                  'bg-warning-100 text-warning-800': selectedTask.status === 'pending',
                  'bg-primary-100 text-primary-800': selectedTask.status === 'in_progress',
                  'bg-secondary-100 text-secondary-800': selectedTask.status === 'completed'
                }"
              >
                {{ 
                  selectedTask.status === 'pending' ? 'Pendiente' : 
                  selectedTask.status === 'in_progress' ? 'En Progreso' : 
                  'Completada' 
                }}
              </span>
            </div>
          </div>
          <div class="text-right">
            <div class="text-2xl font-bold text-primary-600">{{ selectedTask.percentage }}%</div>
            <div class="text-sm text-dark-600">Progreso</div>
          </div>
        </div>

        <!-- Barra de progreso -->
        <div class="w-full bg-dark-200 rounded-full h-3">
          <ProgressBar :progress="Number(selectedTask.percentage)" size="md" />
        </div>

        <!-- Información de la tarea -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-dark-700 mb-1">Descripción</label>
              <p class="text-dark-900 bg-dark-50 rounded-lg p-3">{{ selectedTask.description || 'Sin descripción' }}</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-dark-700 mb-1">Proyecto</label>
              <p class="text-dark-900">{{ selectedTask.project?.name }}</p>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-dark-700 mb-1">Asignado a</label>
              <p class="text-dark-900">{{ selectedTask.assigned_user?.name || 'Sin asignar' }}</p>
              <p v-if="selectedTask.assigned_user?.email" class="text-sm text-dark-600">{{ selectedTask.assigned_user.email }}</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-dark-700 mb-1">Fecha de vencimiento</label>
              <p class="text-dark-900">{{ selectedTask.due_date || 'Sin fecha límite' }}</p>
            </div>
          </div>
        </div>

        <!-- Fechas de creación y actualización -->
        <div class="border-t border-dark-200 pt-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-dark-600">
            <div>
              <span class="font-medium">Creada:</span> {{ selectedTask.created_at }}
            </div>
            <div>
              <span class="font-medium">Última actualización:</span> {{ selectedTask.updated_at }}
            </div>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-dark-200">
          <AppButton
            variant="secondary"
            @click="showViewModal = false"
          >
            Cerrar
          </AppButton>
          <AppButton
            variant="primary"
            @click="() => {
              showViewModal = false
              handleEditTask(selectedTask)
            }"
          >
            Editar Tarea
          </AppButton>
        </div>
      </div>
    </AppModal>

    <!-- Modal de confirmación para eliminar -->
    <AppModal
      v-model="showDeleteModal"
      title="Confirmar Eliminación"
      :maxWidth="'md'"
    >
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-danger-100 mb-4">
          <svg class="h-6 w-6 text-danger-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-dark-900 mb-2">¿Eliminar tarea?</h3>
        <p class="text-sm text-dark-500 mb-6">
          ¿Estás seguro de que deseas eliminar la tarea "{{ selectedTask?.name }}"? 
          Esta acción no se puede deshacer y afectará el progreso del proyecto.
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
            @click="confirmDeleteTask"
          >
            Eliminar
          </AppButton>
        </div>
      </div>
    </AppModal>
  </div>
</template>