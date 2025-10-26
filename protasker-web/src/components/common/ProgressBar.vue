<template>
  <div 
    class="rounded-full transition-all duration-300"
    :class="[
      heightClass,
      progressClass
    ]"
    :style="progressStyle"
  />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  progress: {
    type: Number,
    default: 0
  },
  size: {
    type: String,
    default: 'sm',
    validator: (value) => ['sm', 'md'].includes(value)
  }
})

const heightClass = computed(() => {
  return props.size === 'md' ? 'h-3' : 'h-2'
})

const progressStyle = computed(() => ({
  width: `${Math.max(0, Math.min(100, props.progress || 0))}%`
}))

const progressClass = computed(() => {
  if (props.progress >= 100) return 'bg-secondary-500'
  if (props.progress >= 50) return 'bg-primary-500'
  return 'bg-warning-500'
})
</script>