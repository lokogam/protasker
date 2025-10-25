<script setup>
import { ref, computed, onMounted } from 'vue'
import { useTasks } from '../../composables/useTasks.js'
import AppInput from '../common/AppInput.vue'
import AppButton from '../common/AppButton.vue'

const props = defineProps({
  task: {
    type: Object,
    default: null
  },
  projectId: {
    type: [Number, String],
    default: null
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'cancel'])

const { fetchAvailableUsers, users } = useTasks()

// Form data reactivo
const form = ref({
  name: props.task?.name || '',
  description: props.task?.description || '',
  project_id: props.task?.project_id || props.projectId || '',
  assigned_to: props.task?.assigned_to || '',
  status: props.task?.status || 'pending',
  percentage: props.task?.percentage || 0,
  due_date: props.task?.due_date || ''
})

// Errores de validación
const errors = ref({})

// Computed para saber si es edición
const isEditing = computed(() => !!props.task)

// Opciones de estado
const statusOptions = [
  { value: 'pending', label: 'Pendiente' },
  { value: 'in_progress', label: 'En Progreso' },
  { value: 'completed', label: 'Completada' }
]

// Validación del formulario
const validateForm = () => {
  errors.value = {}
  
  if (!form.value.name.trim()) {
    errors.value.name = 'El nombre de la tarea es requerido'
  }
  
  if (!form.value.description.trim()) {
    errors.value.description = 'La descripción es requerida'
  }

  if (!form.value.project_id) {
    errors.value.project_id = 'El proyecto es requerido'
  }
  
  if (form.value.percentage < 0 || form.value.percentage > 100) {
    errors.value.percentage = 'El porcentaje debe estar entre 0 y 100'
  }
  
  if (form.value.due_date) {
    const dueDate = new Date(form.value.due_date)
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    
    if (dueDate < today) {
      errors.value.due_date = 'La fecha de vencimiento no puede ser anterior a hoy'
    }
  }
  
  return Object.keys(errors.value).length === 0
}

// Enviar formulario
const handleSubmit = () => {
  if (validateForm()) {
    // Limpiar campos vacíos opcionales
    const submitData = { ...form.value }
    if (!submitData.assigned_to) delete submitData.assigned_to
    if (!submitData.due_date) delete submitData.due_date
    if (!submitData.percentage) submitData.percentage = 0
    
    emit('submit', submitData)
  }
}

// Limpiar errores cuando el usuario escribe
const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

// Cargar usuarios al montar el componente
onMounted(() => {
  fetchAvailableUsers()
})
</script>

<template>
  <div class="space-y-6">
    <!-- Título del formulario -->
    <div class="text-center">
      <h2 class="text-2xl font-bold text-dark-900">
        {{ isEditing ? 'Editar Tarea' : 'Crear Nueva Tarea' }}
      </h2>
      <p class="text-dark-600 mt-2">
        {{ isEditing ? 'Modifica los datos de la tarea' : 'Completa la información de la nueva tarea' }}
      </p>
    </div>

    <!-- Formulario -->
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Nombre de la tarea -->
      <AppInput
        v-model="form.name"
        label="Nombre de la Tarea"
        placeholder="Ingrese el nombre de la tarea"
        :error="errors.name"
        required
        @input="clearError('name')"
      />

      <!-- Descripción -->
      <div>
        <label class="block text-sm font-medium text-dark-700 mb-2">
          Descripción *
        </label>
        <textarea
          v-model="form.description"
          rows="4"
          class="w-full px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"
          :class="{ 'border-danger-500 focus:ring-danger-500 focus:border-danger-500': errors.description }"
          placeholder="Describe la tarea..."
          @input="clearError('description')"
        ></textarea>
        <p v-if="errors.description" class="mt-1 text-sm text-danger-600">
          {{ errors.description }}
        </p>
      </div>

      <!-- Grid para campos en dos columnas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Usuario asignado -->
        <div>
          <label class="block text-sm font-medium text-dark-700 mb-2">
            Asignar a Usuario
          </label>
          <select
            v-model="form.assigned_to"
            class="w-full px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            :class="{ 'border-danger-500 focus:ring-danger-500 focus:border-danger-500': errors.assigned_to }"
            @change="clearError('assigned_to')"
          >
            <option value="">Sin asignar</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
          <p v-if="errors.assigned_to" class="mt-1 text-sm text-danger-600">
            {{ errors.assigned_to }}
          </p>
        </div>

        <!-- Estado -->
        <div>
          <label class="block text-sm font-medium text-dark-700 mb-2">
            Estado
          </label>
          <select
            v-model="form.status"
            class="w-full px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          >
            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>
      </div>

      <!-- Grid para porcentaje y fecha -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Porcentaje del proyecto -->
        <div>
          <label class="block text-sm font-medium text-dark-700 mb-2">
            Peso en el Proyecto (%)
          </label>
          <input
            v-model.number="form.percentage"
            type="number"
            min="0"
            max="100"
            step="0.01"
            class="w-full px-3 py-2 border border-dark-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            :class="{ 'border-danger-500 focus:ring-danger-500 focus:border-danger-500': errors.percentage }"
            placeholder="0.00"
            @input="clearError('percentage')"
          >
          <p v-if="errors.percentage" class="mt-1 text-sm text-danger-600">
            {{ errors.percentage }}
          </p>
          <p class="mt-1 text-xs text-dark-500">
            Porcentaje que representa esta tarea en el progreso total del proyecto
          </p>
        </div>

        <!-- Fecha de vencimiento -->
        <AppInput
          v-model="form.due_date"
          type="date"
          label="Fecha de Vencimiento"
          :error="errors.due_date"
          @input="clearError('due_date')"
        />
      </div>

      <!-- Campo oculto para project_id si no es edición -->
      <input v-if="projectId" type="hidden" v-model="form.project_id">

      <!-- Botones de acción -->
      <div class="flex justify-end space-x-3 pt-6 border-t border-dark-200">
        <AppButton
          type="button"
          variant="secondary"
          @click="emit('cancel')"
          :disabled="isLoading"
        >
          Cancelar
        </AppButton>
        
        <AppButton
          type="submit"
          variant="primary"
          :loading="isLoading"
          :disabled="isLoading"
        >
          {{ isEditing ? 'Actualizar' : 'Crear' }} Tarea
        </AppButton>
      </div>
    </form>
  </div>
</template>