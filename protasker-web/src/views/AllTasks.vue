<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useTasks } from '../composables/useTasks.js'
import { useUsers } from '../composables/useUsers.js'
import { useRouter } from 'vue-router'
import TaskCard from '../components/cards/TaskCard.vue'
import TaskForm from '../components/forms/TaskForm.vue'
import AppButton from '../components/common/AppButton.vue'
import AppModal from '../components/common/AppModal.vue'
import UserSearchSelect from '../components/common/UserSearchSelect.vue'

const { user, logout } = useAuth()
const router = useRouter()
const { 
  tasks, 
  loading, 
  fetchAllTasks, 
  updateTask, 
  deleteTask 
} = useTasks()

const {
  users,
  loading: usersLoading,
  fetchUsers,
  searchUsers
} = useUsers()

// Estados del componente
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showAssignModal = ref(false)
const showViewModal = ref(false)
const selectedTask = ref(null)
const formLoading = ref(false)
const filterStatus = ref('all')
const filterUser = ref('')
const searchQuery = ref('')
const availableUsers = ref([])

// Computed para verificar si es administrador
const isAdmin = computed(() => {
  return user.value?.roles?.[0]?.name === 'administrador'
})

// Computed para todas las tareas filtradas
const allTasks = computed(() => {
  let filtered = tasks.value

  // Filtrar por estado
  if (filterStatus.value !== 'all') {
    filtered = filtered.filter(task => task.status === filterStatus.value)
  }

  // Filtrar por usuario (solo para administradores)
  if (isAdmin.value && filterUser.value && filterUser.value !== 'all') {
    filtered = filtered.filter(task => task.assigned_user?.id.toString() === filterUser.value)
  }

  // Filtrar por búsqueda
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(task => 
      task.name.toLowerCase().includes(query) ||
      task.description?.toLowerCase().includes(query) ||
      task.project?.name?.toLowerCase().includes(query) ||
      task.assigned_user?.name?.toLowerCase().includes(query)
    )
  }

  return filtered
})

// Computed para estadísticas
const taskStats = computed(() => {
  return {
    total: tasks.value.length,
    pending: tasks.value.filter(t => t.status === 'pending').length,
    in_progress: tasks.value.filter(t => t.status === 'in_progress').length,
    completed: tasks.value.filter(t => t.status === 'completed').length
  }
})

// Opciones de filtro
const statusOptions = [
  { value: 'all', label: 'Todos los Estados' },
  { value: 'pending', label: 'Pendientes' },
  { value: 'in_progress', label: 'En Progreso' },
  { value: 'completed', label: 'Completadas' }
]

// Funciones para manejar tareas
const handleEditTask = (task) => {
  // Resetear estado primero
  resetModalState()
  
  // Usar nextTick para asegurar que el reset se complete antes de asignar nueva tarea
  nextTick(() => {
    selectedTask.value = task
    showEditModal.value = true
  })
}

const handleCancelEdit = () => {
  showEditModal.value = false
  selectedTask.value = null
}

const resetModalState = () => {
  showEditModal.value = false
  showViewModal.value = false
  showDeleteModal.value = false
  showAssignModal.value = false
  selectedTask.value = null
}

const handleUpdateTask = async (taskData) => {
  formLoading.value = true
  try {
    await updateTask(selectedTask.value.id, taskData)
    showEditModal.value = false
    selectedTask.value = null
    await fetchAllTasks() // Refrescar lista
  } catch (error) {
    console.error('Error updating task:', error)
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
    await fetchAllTasks() // Refrescar lista
  } catch (error) {
    console.error('Error deleting task:', error)
  }
}

const handleViewTask = (task) => {
  // Resetear estado primero
  resetModalState()
  
  // Usar nextTick para asegurar que el reset se complete antes de asignar nueva tarea
  nextTick(() => {
    selectedTask.value = task
    showViewModal.value = true
  })
}

const handleAssignTask = (task) => {
  selectedTask.value = task
  showAssignModal.value = true
}

const handleEditFromView = () => {
  showViewModal.value = false
  // Pequeña demora para asegurar que el modal de vista se cierre completamente
  setTimeout(() => {
    showEditModal.value = true
  }, 100)
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

// Navegación
const goToDashboard = () => {
  if (user.value?.roles?.[0]?.name === 'administrador') {
    router.push('/dashboard/admin')
  } else {
    router.push('/dashboard/developer')
  }
}

// Cargar datos al montar el componente
onMounted(async () => {
  await fetchAllTasks()
  
  // Solo cargar usuarios si es administrador
  if (isAdmin.value) {
    await fetchUsers()
  }
})

// Watchers para limpiar estado cuando se cierran los modales
watch(showEditModal, (newValue) => {
  if (!newValue) {
    // Limpiar selectedTask cuando se cierra el modal de edición
    setTimeout(() => {
      selectedTask.value = null
    }, 300) // Esperar a que se complete la animación del modal
  }
})

watch(showViewModal, (newValue) => {
  if (!newValue) {
    // Limpiar selectedTask cuando se cierra el modal de vista
    setTimeout(() => {
      if (!showEditModal.value) {
        selectedTask.value = null
      }
    }, 300)
  }
})
</script>

<template>
  <div class="min-h-screen bg-dark-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header con navegación -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6 mb-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-dark-600 mb-4">
          <button 
            @click="goToDashboard"
            class="hover:text-primary-600 transition-colors"
          >
            Dashboard
          </button>
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
          <span class="text-dark-900 font-medium">Todas las Tareas</span>
        </nav>
        
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-dark-900">Todas las Tareas</h1>
            <p class="text-dark-600 mt-2">Vista general de todas las tareas del sistema</p>
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

      <!-- Estadísticas Generales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-dark-600">Total Tareas</p>
              <p class="text-2xl font-bold text-dark-900">{{ taskStats.total }}</p>
            </div>
            <div class="w-12 h-12 bg-info-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-info-600" fill="currentColor" viewBox="0 0 20 20">
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
                placeholder="Buscar tareas, descripción, proyecto o usuario..."
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

            <!-- Filtro por usuario (solo para admin) -->
            <select
              v-if="isAdmin"
              v-model="filterUser"
              class="px-4 py-2 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
              <option value="all">Todos los Usuarios</option>
              <option v-for="user in users" :key="user.id" :value="user.id.toString()">
                {{ user.name }}
              </option>
            </select>
          </div>

          <div class="text-sm text-dark-500">
            Mostrando {{ allTasks.length }} de {{ taskStats.total }} tareas
          </div>
        </div>
      </div>

      <!-- Lista de Tareas -->
      <div class="bg-white rounded-lg shadow-sm border border-dark-200 p-6">
        <h2 class="text-xl font-semibold text-dark-900 mb-6">Lista de Tareas</h2>
        
        <!-- Loading state -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>

        <!-- Empty state -->
        <div v-else-if="allTasks.length === 0 && !searchQuery && filterStatus === 'all' && filterUser === 'all'" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-dark-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M9 12h6m-6 4h6m2 5.291A7.962 7.962 0 0112 15c-2.347 0-4.518.893-6.14 2.36L12 21l6.14-3.64A7.962 7.962 0 0118 13.291zM6 9a6 6 0 1112 0v3a6 6 0 01-12 0V9z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-dark-900">No hay tareas</h3>
          <p class="mt-1 text-sm text-dark-500">Aún no se han creado tareas en el sistema.</p>
        </div>

        <!-- No results state -->
        <div v-else-if="allTasks.length === 0" class="text-center py-12">
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
              @click="searchQuery = ''; filterStatus = 'all'; filterUser = 'all'"
            >
              Limpiar Filtros
            </AppButton>
          </div>
        </div>

        <!-- Grid de tareas -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <TaskCard
            v-for="task in allTasks"
            :key="task.id"
            :task="task"
            :show-project="true"
            @edit="handleEditTask"
            @delete="handleDeleteTask"
            @view="handleViewTask"
            @assign="handleAssignTask"
          />
        </div>
      </div>
    </div>

    <!-- Modal para editar tarea -->
    <AppModal
      v-model="showEditModal"
      title="Editar Tarea"
      :maxWidth="'2xl'"
    >
      <TaskForm
        :key="selectedTask?.id || 'new'"
        :task="selectedTask"
        :is-loading="formLoading"
        @submit="handleUpdateTask"
        @cancel="handleCancelEdit"
      />
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
          Esta acción no se puede deshacer.
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

    <!-- Modal para ver detalles de la tarea -->
    <AppModal
      v-model="showViewModal"
      title="Detalles de la Tarea"
      :maxWidth="'2xl'"
    >
      <div v-if="selectedTask" class="space-y-6">
        <!-- Información básica -->
        <div class="border-b border-dark-200 pb-4">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-xl font-semibold text-dark-900">{{ selectedTask.name }}</h3>
            <span :class="`px-3 py-1 rounded-full text-sm font-medium border ${
              selectedTask.status === 'completed' ? 'bg-secondary-100 text-secondary-800 border-secondary-200' :
              selectedTask.status === 'in_progress' ? 'bg-primary-100 text-primary-800 border-primary-200' :
              selectedTask.status === 'pending' ? 'bg-warning-100 text-warning-800 border-warning-200' :
              'bg-dark-100 text-dark-800 border-dark-200'
            }`">
              {{
                selectedTask.status === 'completed' ? 'Completada' :
                selectedTask.status === 'in_progress' ? 'En Progreso' :
                selectedTask.status === 'pending' ? 'Pendiente' :
                selectedTask.status
              }}
            </span>
          </div>
          
          <p v-if="selectedTask.description" class="text-dark-600">
            {{ selectedTask.description }}
          </p>
          <p v-else class="text-dark-500 italic">Sin descripción</p>
        </div>

        <!-- Información del proyecto -->
        <div v-if="selectedTask.project" class="bg-primary-50 rounded-lg p-4">
          <h4 class="font-medium text-primary-900 mb-2">Proyecto</h4>
          <p class="text-primary-800">{{ selectedTask.project.name }}</p>
          <p v-if="selectedTask.project.description" class="text-primary-700 text-sm mt-1">
            {{ selectedTask.project.description }}
          </p>
        </div>

        <!-- Detalles de asignación y fechas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Asignación -->
          <div>
            <h4 class="font-medium text-dark-900 mb-3">Asignación</h4>
            <div class="space-y-2">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-dark-900">
                    {{ selectedTask.assigned_user?.name || 'Sin asignar' }}
                  </p>
                  <p v-if="selectedTask.assigned_user?.email" class="text-sm text-dark-600">
                    {{ selectedTask.assigned_user.email }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Fechas -->
          <div>
            <h4 class="font-medium text-dark-900 mb-3">Cronograma</h4>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-dark-600">Fecha de vencimiento:</span>
                <span class="text-dark-900">
                  {{ selectedTask.due_date ? new Date(selectedTask.due_date).toLocaleDateString('es-ES') : 'Sin fecha' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-dark-600">Creada:</span>
                <span class="text-dark-900">
                  {{ new Date(selectedTask.created_at).toLocaleDateString('es-ES') }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-dark-600">Última actualización:</span>
                <span class="text-dark-900">
                  {{ new Date(selectedTask.updated_at).toLocaleDateString('es-ES') }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Progreso -->
        <div>
          <h4 class="font-medium text-dark-900 mb-3">Progreso</h4>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-dark-600">Porcentaje completado:</span>
              <span class="font-semibold text-dark-900">{{ selectedTask.percentage || 0 }}%</span>
            </div>
            <div class="w-full bg-dark-200 rounded-full h-3">
              <div 
                class="h-3 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 transition-all duration-300"
                :style="`width: ${selectedTask.percentage || 0}%`"
              ></div>
            </div>
            <div v-if="selectedTask.percentage" class="flex justify-between items-center text-sm">
              <span class="text-dark-500">Peso en el proyecto:</span>
              <span class="text-dark-700">{{ selectedTask.percentage }}%</span>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-dark-200">
          <AppButton
            variant="secondary"
            @click="showViewModal = false"
          >
            Cerrar
          </AppButton>
          <AppButton
            variant="warning"
            @click="handleEditFromView"
          >
            Editar Tarea
          </AppButton>
        </div>
      </div>
    </AppModal>
  </div>
</template>