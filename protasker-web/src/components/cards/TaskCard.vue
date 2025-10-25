<script setup>
import { computed } from 'vue'
import AppButton from '../common/AppButton.vue'

const props = defineProps({
  task: {
    type: Object,
    required: true
  },
  showActions: {
    type: Boolean,
    default: true
  },
  showProject: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['edit', 'delete', 'view', 'assign'])

// Computed para el color del estado
const statusColor = computed(() => {
  const status = props.task.status
  switch (status) {
    case 'completed': return 'bg-secondary-100 text-secondary-800 border-secondary-200'
    case 'in_progress': return 'bg-primary-100 text-primary-800 border-primary-200'
    case 'pending': return 'bg-warning-100 text-warning-800 border-warning-200'
    default: return 'bg-dark-100 text-dark-800 border-dark-200'
  }
})

// Computed para el texto del estado
const statusText = computed(() => {
  const status = props.task.status
  switch (status) {
    case 'completed': return 'Completada'
    case 'in_progress': return 'En Progreso'
    case 'pending': return 'Pendiente'
    default: return status
  }
})

// Computed para el color de prioridad por fecha de vencimiento
const priorityColor = computed(() => {
  if (!props.task.due_date) return ''
  
  const dueDate = new Date(props.task.due_date)
  const today = new Date()
  const diffTime = dueDate.getTime() - today.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays < 0) return 'border-l-4 border-l-danger-500' // Vencida
  if (diffDays <= 2) return 'border-l-4 border-l-warning-500' // Próxima a vencer
  if (diffDays <= 7) return 'border-l-4 border-l-primary-500' // Esta semana
  return 'border-l-4 border-l-secondary-500' // Normal
})

// Formatear fecha
const formatDate = (date) => {
  if (!date) return 'Sin fecha'
  return new Date(date).toLocaleDateString('es-ES')
}

// Calcular días restantes
const daysRemaining = computed(() => {
  if (!props.task.due_date) return null
  
  const dueDate = new Date(props.task.due_date)
  const today = new Date()
  const diffTime = dueDate.getTime() - today.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays < 0) return `Vencida hace ${Math.abs(diffDays)} días`
  if (diffDays === 0) return 'Vence hoy'
  if (diffDays === 1) return 'Vence mañana'
  return `${diffDays} días restantes`
})

// Progreso visual
const progressPercentage = computed(() => {
  if (props.task.status === 'completed') return 100
  if (props.task.status === 'in_progress') return 50
  return 0
})
</script>

<template>
  <div :class="`bg-white rounded-lg shadow-md border border-dark-200 p-6 hover:shadow-lg transition-all duration-200 ${priorityColor}`">
    <!-- Header de la tarea -->
    <div class="flex justify-between items-start mb-4">
      <div class="flex-1">
        <div class="flex items-center space-x-3 mb-2">
          <h3 class="text-lg font-semibold text-dark-900">{{ task.name }}</h3>
          <span :class="`px-2 py-1 rounded-full text-xs font-medium border ${statusColor}`">
            {{ statusText }}
          </span>
        </div>
        
        <p v-if="task.description" class="text-dark-600 text-sm line-clamp-2 mb-3">
          {{ task.description }}
        </p>

        <!-- Información del proyecto si se solicita -->
        <div v-if="showProject && task.project" class="mb-3">
          <span class="text-xs text-dark-500">Proyecto:</span>
          <span class="text-sm font-medium text-primary-600 ml-1">{{ task.project.name }}</span>
        </div>
      </div>
    </div>

    <!-- Barra de progreso visual -->
    <div class="mb-4">
      <div class="flex justify-between items-center mb-2">
        <span class="text-sm font-medium text-dark-700">Progreso</span>
        <span class="text-sm text-dark-600">{{ progressPercentage }}%</span>
      </div>
      <div class="w-full bg-dark-200 rounded-full h-2">
        <div 
          :class="`h-2 rounded-full transition-all duration-300 ${
            task.status === 'completed' ? 'bg-secondary-500' :
            task.status === 'in_progress' ? 'bg-primary-500' : 'bg-dark-300'
          }`"
          :style="{ width: `${progressPercentage}%` }"
        ></div>
      </div>
    </div>

    <!-- Información de la tarea -->
    <div class="space-y-2 mb-4 text-sm">
      <!-- Asignado a -->
      <div class="flex justify-between items-center">
        <span class="text-dark-600">Asignado a:</span>
        <div class="flex items-center space-x-2">
          <span v-if="task.assigned_user" class="text-dark-900 font-medium">
            {{ task.assigned_user.name }}
          </span>
          <span v-else class="text-dark-500 italic">Sin asignar</span>
          <button 
            v-if="showActions"
            @click="emit('assign', task)" 
            class="text-primary-600 hover:text-primary-800 text-xs"
          >
            ✏️
          </button>
        </div>
      </div>

      <!-- Fecha de vencimiento -->
      <div class="flex justify-between items-center">
        <span class="text-dark-600">Vencimiento:</span>
        <div class="text-right">
          <div class="text-dark-900">{{ formatDate(task.due_date) }}</div>
          <div v-if="daysRemaining" :class="`text-xs ${
            task.due_date && new Date(task.due_date) < new Date() ? 'text-danger-600' :
            task.due_date && Math.ceil((new Date(task.due_date) - new Date()) / (1000*60*60*24)) <= 2 ? 'text-warning-600' :
            'text-dark-500'
          }`">
            {{ daysRemaining }}
          </div>
        </div>
      </div>

      <!-- Porcentaje del proyecto -->
      <div v-if="task.percentage" class="flex justify-between">
        <span class="text-dark-600">Peso en proyecto:</span>
        <span class="text-dark-900">{{ task.percentage }}%</span>
      </div>
    </div>

    <!-- Acciones -->
    <div v-if="showActions" class="flex space-x-2 pt-4 border-t border-dark-100">
      <AppButton
        variant="info"
        size="sm"
        @click="emit('view', task)"
      >
        Ver Detalles
      </AppButton>
      
      <AppButton
        variant="warning"
        size="sm"
        @click="emit('edit', task)"
      >
        Editar
      </AppButton>
      
      <AppButton
        variant="danger"
        size="sm"
        @click="emit('delete', task)"
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