<script setup>
import { computed } from 'vue'
import ProgressBar from '../common/ProgressBar.vue'
import AppButton from '../common/AppButton.vue'

const props = defineProps({
  project: {
    type: Object,
    required: true
  },
  showActions: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['edit', 'delete', 'view', 'filter-tasks'])

// Computed para el color de la barra de progreso
const progressColor = computed(() => {
  const progress = props.project.progress || 0
  if (progress < 30) return 'bg-danger-500'
  if (progress < 70) return 'bg-warning-500'
  return 'bg-secondary-500'
})

// Computed para el color del estado
const statusColor = computed(() => {
  const status = props.project.status
  switch (status) {
    case 'completed': return 'bg-secondary-100 text-secondary-800'
    case 'in_progress': return 'bg-primary-100 text-primary-800'
    case 'pending': return 'bg-warning-100 text-warning-800'
    case 'cancelled': return 'bg-danger-100 text-danger-800'
    default: return 'bg-dark-100 text-dark-800'
  }
})

// Computed para el texto del estado
const statusText = computed(() => {
  const status = props.project.status
  switch (status) {
    case 'completed': return 'Completado'
    case 'in_progress': return 'En Progreso'
    case 'pending': return 'Pendiente'
    case 'cancelled': return 'Cancelado'
    default: return status
  }
})

// Formatear fecha
const formatDate = (date) => {
  if (!date) return 'No definida'
  return new Date(date).toLocaleDateString('es-ES')
}

// Computed para estadísticas de tareas
const taskStats = computed(() => {
  const tasks = props.project.tasks || []
  return {
    total: tasks.length,
    completed: tasks.filter(t => t.status === 'completed').length,
    pending: tasks.filter(t => t.status === 'pending').length,
    in_progress: tasks.filter(t => t.status === 'in_progress').length
  }
})

// Manejar click en contador de tareas
const handleTaskFilter = (status) => {
  emit('filter-tasks', { projectId: props.project.id, status })
}
</script>

<template>
  <div class="bg-white rounded-lg shadow-md border border-dark-200 p-6 hover:shadow-lg transition-shadow">
    <!-- Header del proyecto -->
    <div class="flex justify-between items-start mb-4">
      <div class="flex-1">
        <h3 class="text-xl font-semibold text-dark-900 mb-2">{{ project.name }}</h3>
        <p v-if="project.description" class="text-dark-600 text-sm line-clamp-2">
          {{ project.description }}
        </p>
      </div>
      
      <!-- Estado -->
      <span :class="`px-2 py-1 rounded-full text-xs font-medium ${statusColor}`">
        {{ statusText }}
      </span>
    </div>

    <!-- Progreso -->
    <div class="mb-4">
      <div class="flex justify-between items-center mb-2">
        <span class="text-sm font-medium text-dark-700">Progreso</span>
        <span class="text-sm text-dark-600">{{ project.progress || 0 }}%</span>
      </div>
      <div class="w-full bg-dark-200 rounded-full h-2">
        <ProgressBar :progress="Number(project.progress)" />
      </div>
    </div>

    <!-- Información adicional -->
    <div class="space-y-2 mb-4 text-sm">
      <div class="flex justify-between">
        <span class="text-dark-600">Creador:</span>
        <span class="text-dark-900">{{ project.user?.name || 'N/A' }}</span>
      </div>
      
      <div class="flex justify-between">
        <span class="text-dark-600">Inicio:</span>
        <span class="text-dark-900">{{ formatDate(project.start_date) }}</span>
      </div>
      
      <div class="flex justify-between">
        <span class="text-dark-600">Fin:</span>
        <span class="text-dark-900">{{ formatDate(project.end_date) }}</span>
      </div>

      <div v-if="project.tasks_count !== undefined" class="flex justify-between">
        <span class="text-dark-600">Tareas:</span>
        <span class="text-dark-900">
          {{ project.completed_tasks_count || 0 }} / {{ project.tasks_count || 0 }}
        </span>
      </div>
    </div>

    <!-- Contadores de Tareas (si hay tareas disponibles) -->
    <div v-if="project.tasks && project.tasks.length > 0" class="mb-4">
      <h4 class="text-sm font-medium text-dark-700 mb-3">Estado de Tareas</h4>
      <div class="grid grid-cols-3 gap-2">
        <!-- Completadas -->
        <button
          @click="handleTaskFilter('completed')"
          class="bg-secondary-50 hover:bg-secondary-100 p-3 rounded-lg text-center transition-colors cursor-pointer border border-transparent hover:border-secondary-200"
        >
          <p class="text-lg font-bold text-secondary-600">{{ taskStats.completed }}</p>
          <p class="text-xs text-dark-600">Completadas</p>
        </button>
        
        <!-- En progreso -->
        <button
          @click="handleTaskFilter('in_progress')"
          class="bg-primary-50 hover:bg-primary-100 p-3 rounded-lg text-center transition-colors cursor-pointer border border-transparent hover:border-primary-200"
        >
          <p class="text-lg font-bold text-primary-600">{{ taskStats.in_progress }}</p>
          <p class="text-xs text-dark-600">En Progreso</p>
        </button>
        
        <!-- Pendientes -->
        <button
          @click="handleTaskFilter('pending')"
          class="bg-warning-50 hover:bg-warning-100 p-3 rounded-lg text-center transition-colors cursor-pointer border border-transparent hover:border-warning-200"
        >
          <p class="text-lg font-bold text-warning-600">{{ taskStats.pending }}</p>
          <p class="text-xs text-dark-600">Pendientes</p>
        </button>
      </div>
      
      <!-- Total de tareas -->
      <button
        @click="handleTaskFilter('all')"
        class="w-full mt-2 bg-dark-50 hover:bg-dark-100 p-2 rounded-lg text-center transition-colors cursor-pointer border border-transparent hover:border-dark-200"
      >
        <p class="text-sm font-medium text-dark-700">
          Total: {{ taskStats.total }} tareas
        </p>
      </button>
    </div>

    <!-- Acciones -->
    <div v-if="showActions" class="flex space-x-2 pt-4 border-t border-dark-100">
      <AppButton
        variant="info"
        size="sm"
        @click="emit('view', project)"
      >
        Ver Tareas
      </AppButton>
      
      <AppButton
        variant="warning"
        size="sm"
        @click="emit('edit', project)"
      >
        Editar
      </AppButton>
      
      <AppButton
        variant="danger"
        size="sm"
        @click="emit('delete', project)"
      >
        Eliminar
      </AppButton>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>