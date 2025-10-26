<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  users: {
    type: Array,
    default: () => []
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Buscar usuario...'
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'select'])

// Estado del componente
const searchQuery = ref('')
const isOpen = ref(false)
const selectedIndex = ref(-1)

// Referencias DOM
const inputRef = ref(null)
const dropdownRef = ref(null)

// Computed para usuarios filtrados
const filteredUsers = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.users.slice(0, 10) // Mostrar solo los primeros 10 si no hay búsqueda
  }
  
  const query = searchQuery.value.toLowerCase()
  return props.users.filter(user => 
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  ).slice(0, 10) // Limitar resultados
})

// Computed para el usuario seleccionado
const selectedUser = computed(() => {
  if (!props.modelValue) return null
  return props.users.find(user => 
    user.id.toString() === props.modelValue.toString()
  )
})

// Computed para el texto del input
const inputText = computed({
  get() {
    if (selectedUser.value && !isOpen.value) {
      return selectedUser.value.name
    }
    return searchQuery.value
  },
  set(value) {
    searchQuery.value = value
  }
})

// Watchers
watch(() => props.modelValue, (newValue) => {
  if (!newValue && selectedUser.value) {
    searchQuery.value = ''
  }
})

// Métodos
const openDropdown = () => {
  if (props.disabled) return
  isOpen.value = true
  selectedIndex.value = -1
}

const closeDropdown = () => {
  isOpen.value = false
  selectedIndex.value = -1
  
  // Si no hay selección válida, limpiar el input
  if (!selectedUser.value) {
    searchQuery.value = ''
  }
}

const selectUser = (user) => {
  emit('update:modelValue', user.id.toString())
  emit('select', user)
  searchQuery.value = user.name
  closeDropdown()
}

const clearSelection = () => {
  emit('update:modelValue', '')
  emit('select', null)
  searchQuery.value = ''
  inputRef.value?.focus()
}

const handleKeyDown = (event) => {
  if (!isOpen.value) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedIndex.value = Math.min(selectedIndex.value + 1, filteredUsers.value.length - 1)
      break
    case 'ArrowUp':
      event.preventDefault()
      selectedIndex.value = Math.max(selectedIndex.value - 1, -1)
      break
    case 'Enter':
      event.preventDefault()
      if (selectedIndex.value >= 0 && filteredUsers.value[selectedIndex.value]) {
        selectUser(filteredUsers.value[selectedIndex.value])
      }
      break
    case 'Escape':
      event.preventDefault()
      closeDropdown()
      break
  }
}

const handleInput = (event) => {
  searchQuery.value = event.target.value
  if (!isOpen.value) {
    openDropdown()
  }
  selectedIndex.value = -1
  
  // Si el usuario está escribiendo, limpiar selección previa
  if (event.target.value !== selectedUser.value?.name) {
    emit('update:modelValue', '')
  }
}

// Cerrar dropdown al hacer click fuera
const handleClickOutside = (event) => {
  if (!dropdownRef.value?.contains(event.target) && !inputRef.value?.contains(event.target)) {
    closeDropdown()
  }
}

// Lifecycle
import { onMounted, onUnmounted } from 'vue'

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="relative">
    <!-- Input de búsqueda -->
    <div class="relative">
      <input
        ref="inputRef"
        :value="inputText"
        :placeholder="placeholder"
        :disabled="disabled"
        @input="handleInput"
        @focus="openDropdown"
        @keydown="handleKeyDown"
        class="w-full px-4 py-2 pr-10 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-dark-100 disabled:cursor-not-allowed"
        autocomplete="off"
      />
      
      <!-- Icono de búsqueda o clear -->
      <div class="absolute inset-y-0 right-0 flex items-center pr-3">
        <button
          v-if="selectedUser"
          @click="clearSelection"
          class="text-dark-400 hover:text-dark-600 transition-colors"
          type="button"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <svg v-else class="w-5 h-5 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>

    <!-- Dropdown de resultados -->
    <div
      v-if="isOpen"
      ref="dropdownRef"
      class="absolute z-50 w-full mt-1 bg-white border border-dark-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <!-- Loading state -->
      <div v-if="props.users.length === 0" class="px-4 py-3 text-dark-500 text-center">
        Cargando usuarios...
      </div>
      
      <!-- No results -->
      <div v-else-if="filteredUsers.length === 0" class="px-4 py-3 text-dark-500 text-center">
        No se encontraron usuarios
      </div>
      
      <!-- Results -->
      <div v-else>
        <button
          v-for="(user, index) in filteredUsers"
          :key="user.id"
          @click="selectUser(user)"
          :class="[
            'w-full px-4 py-3 text-left hover:bg-primary-50 transition-colors border-b border-dark-100 last:border-b-0',
            index === selectedIndex ? 'bg-primary-50' : ''
          ]"
          type="button"
        >
          <div class="flex items-center space-x-3">
            <!-- Avatar -->
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
            
            <!-- User info -->
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-dark-900 truncate">{{ user.name }}</p>
              <p class="text-xs text-dark-600 truncate">{{ user.email }}</p>
              <div class="flex items-center space-x-2 mt-1">
                <span 
                  v-for="role in user.roles" 
                  :key="role.id"
                  :class="[
                    'inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium',
                    role.name === 'administrador' ? 'bg-danger-100 text-danger-800' : 'bg-primary-100 text-primary-800'
                  ]"
                >
                  {{ role.name }}
                </span>
              </div>
            </div>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Asegurar que el dropdown esté por encima de otros elementos */
.relative {
  z-index: 10;
}
</style>