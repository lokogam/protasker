<script setup>
import { ref, computed } from 'vue'
import AppInput from '../common/AppInput.vue'
import AppButton from '../common/AppButton.vue'

const props = defineProps({
  project: {
    type: Object,
    default: null
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'cancel'])

// Form data reactivo
const form = ref({
  name: props.project?.name || '',
  description: props.project?.description || '',
  start_date: props.project?.start_date || '',
  end_date: props.project?.end_date || '',
  status: props.project?.status || 'pending'
})

// Errores de validación
const errors = ref({})

// Computed para saber si es edición
const isEditing = computed(() => !!props.project)

// Opciones de estado
const statusOptions = [
  { value: 'pending', label: 'Pendiente' },
  { value: 'in_progress', label: 'En Progreso' },
  { value: 'completed', label: 'Completado' },
  { value: 'cancelled', label: 'Cancelado' }
]

// Validación del formulario
const validateForm = () => {
  errors.value = {}
  
  if (!form.value.name.trim()) {
    errors.value.name = 'El nombre del proyecto es requerido'
  }
  
  if (!form.value.description.trim()) {
    errors.value.description = 'La descripción es requerida'
  }
  
  if (!form.value.start_date) {
    errors.value.start_date = 'La fecha de inicio es requerida'
  }
  
  if (!form.value.end_date) {
    errors.value.end_date = 'La fecha de fin es requerida'
  }
  
  if (form.value.start_date && form.value.end_date) {
    if (new Date(form.value.start_date) >= new Date(form.value.end_date)) {
      errors.value.end_date = 'La fecha de fin debe ser posterior a la de inicio'
    }
  }
  
  return Object.keys(errors.value).length === 0
}

// Enviar formulario
const handleSubmit = () => {
  if (validateForm()) {
    emit('submit', { ...form.value })
  }
}

// Limpiar errores cuando el usuario escribe
const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Título del formulario -->
    <div class="text-center">
      <h2 class="text-2xl font-bold text-dark-900">
        {{ isEditing ? 'Editar Proyecto' : 'Crear Nuevo Proyecto' }}
      </h2>
      <p class="text-dark-600 mt-2">
        {{ isEditing ? 'Modifica los datos del proyecto' : 'Completa la información del nuevo proyecto' }}
      </p>
    </div>

    <!-- Formulario -->
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Nombre del proyecto -->
      <AppInput
        v-model="form.name"
        label="Nombre del Proyecto"
        placeholder="Ingrese el nombre del proyecto"
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
          placeholder="Describe el proyecto..."
          @input="clearError('description')"
        ></textarea>
        <p v-if="errors.description" class="mt-1 text-sm text-danger-600">
          {{ errors.description }}
        </p>
      </div>

      <!-- Fechas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <AppInput
          v-model="form.start_date"
          type="date"
          label="Fecha de Inicio"
          :error="errors.start_date"
          required
          @input="clearError('start_date')"
        />

        <AppInput
          v-model="form.end_date"
          type="date"
          label="Fecha de Fin"
          :error="errors.end_date"
          required
          @input="clearError('end_date')"
        />
      </div>

      <!-- Estado (solo en edición) -->
      <div v-if="isEditing">
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
          {{ isEditing ? 'Actualizar' : 'Crear' }} Proyecto
        </AppButton>
      </div>
    </form>
  </div>
</template>