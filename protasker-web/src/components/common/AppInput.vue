<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const updateValue = (event) => {
  emit('update:modelValue', event.target.value)
}

const inputClasses = computed(() => {
  const base = 'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors'
  
  if (props.error) {
    return `${base} border-danger-300 focus:ring-danger-500 focus:border-danger-500`
  }
  
  return `${base} border-dark-300 focus:ring-primary-500 focus:border-primary-500`
})
</script>

<template>
  <div class="space-y-1">
    <!-- Label -->
    <label v-if="label" class="block text-sm font-medium text-dark-700">
      {{ label }}
      <span v-if="required" class="text-danger-500">*</span>
    </label>

    <!-- Input -->
    <input
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :class="inputClasses"
      @input="updateValue"
    />

    <!-- Error Message -->
    <p v-if="error" class="text-sm text-danger-600">
      {{ error }}
    </p>
  </div>
</template>

<script>
import { computed } from 'vue'
export default {
  name: 'AppInput'
}
</script>