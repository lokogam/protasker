<script setup>
import { onMounted } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useRouter } from 'vue-router'

const { user, logout } = useAuth()
const router = useRouter()

// Función para hacer logout
const handleLogout = () => {
  logout()
  router.push('/login')
}

// Redireccionar al dashboard específico según el rol del usuario
onMounted(async () => {
  if (user.value?.roles?.[0]?.name === 'administrador') {
    router.replace('/dashboard/admin')
  } else if (user.value?.roles?.[0]?.name === 'desarrollador') {
    router.replace('/dashboard/developer')
  }
  // Si no tiene rol específico, se queda en este dashboard genérico
})
</script>

<template>
  <div class="min-h-screen bg-dark-50">
    <!-- Header Simple -->
    <header class="bg-white shadow-sm border-b border-dark-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-primary-600">Protasker</h1>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-dark-600">Hola, {{ user?.name }}</span>
            <button 
              @click="handleLogout"
              class="bg-danger-600 hover:bg-danger-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
            >
              Cerrar Sesión
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content - Dashboard en Blanco -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="text-center">
        <!-- Mensaje de Éxito -->
        <div class="bg-secondary-50 border border-secondary-200 text-secondary-800 px-6 py-4 rounded-lg mb-8 inline-block">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-secondary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium text-lg">¡Login exitoso!</span>
          </div>
        </div>

        <!-- Info del Usuario -->
        <div class="bg-white rounded-lg shadow-md p-8 max-w-md mx-auto">
          <h2 class="text-2xl font-bold text-dark-900 mb-4">Dashboard</h2>
          
          <div class="space-y-3 text-left">
            <div class="flex justify-between">
              <span class="font-medium text-dark-600">Usuario:</span>
              <span class="text-dark-900">{{ user?.name }}</span>
            </div>
            
            <div class="flex justify-between">
              <span class="font-medium text-dark-600">Email:</span>
              <span class="text-dark-900">{{ user?.email }}</span>
            </div>
            
            <div class="flex justify-between">
              <span class="font-medium text-dark-600">Estado:</span>
              <span class="text-secondary-600 font-medium">✅ Autenticado</span>
            </div>
          </div>
        </div>

        <!-- Mensaje Simple -->
        <div class="mt-8">
          <p class="text-dark-600 text-lg">
            Bienvenido al dashboard de Protasker
          </p>
          <p class="text-dark-500 mt-2">
            Tu sesión está activa y funcionando correctamente.
          </p>
        </div>
      </div>
    </main>
  </div>
</template>