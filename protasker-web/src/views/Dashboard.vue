<script setup>
import { onMounted } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useRouter } from 'vue-router'

const { user } = useAuth()
const router = useRouter()

// Redireccionar inmediatamente al dashboard específico según el rol del usuario
onMounted(async () => {
  if (user.value?.roles?.[0]?.name === 'administrador') {
    router.replace('/dashboard/admin')
  } else if (user.value?.roles?.[0]?.name === 'desarrollador') {
    router.replace('/dashboard/developer')
  } else {
    // Si no tiene rol específico, redirigir a login
    router.replace('/login')
  }
})
</script>

<template>
  <div class="min-h-screen bg-dark-50 flex items-center justify-center">
    <!-- Loading simple mientras se redirige -->
    <div class="text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
      <p class="text-dark-600 mt-4">Redirigiendo...</p>
    </div>
  </div>
</template>