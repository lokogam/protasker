<script setup>
import { computed } from 'vue'
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

const emit = defineEmits(['edit', 'delete', 'view'])

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
        <div 
          :class="`h-2 rounded-full transition-all duration-300 ${progressColor}`"
          :style="{ width: `${project.progress || 0}%` }"
        ></div>
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
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>