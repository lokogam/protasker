<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth.js'

const router = useRouter()
const { login, loading, error } = useAuth()

// Estado del formulario
const email = ref('')
const password = ref('')

// Función para manejar el submit
const handleLogin = async () => {
  const success = await login(email.value, password.value)
  if (success) {
    router.push('/dashboard')
  }
}
</script>

<template>
  <div class="min-h-screen bg-dark-50 flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
      <!-- Header -->
      <div class="text-center">
        <h1 class="text-3xl font-bold text-primary-600 mb-2">Protasker</h1>
        <h2 class="text-xl font-semibold text-dark-900 mb-4">Iniciar Sesión</h2>
        <p class="text-dark-600">Ingresa tus credenciales para acceder</p>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="handleLogin" class="bg-white rounded-lg shadow-md p-6 space-y-6">
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-dark-700 mb-2">
            Email
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="w-full px-3 py-2 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            placeholder="tu@email.com"
          />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-dark-700 mb-2">
            Contraseña
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="w-full px-3 py-2 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            placeholder="••••••••"
          />
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-danger-50 border border-danger-200 text-danger-700 px-4 py-3 rounded-lg">
          {{ error }}
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-primary-300 text-white font-medium py-2 px-4 rounded-lg transition-colors"
        >
          <span v-if="loading">Iniciando sesión...</span>
          <span v-else>Iniciar Sesión</span>
        </button>
      </form>

      <!-- Link para registro -->
      <div class="text-center mt-6">
        <p class="text-sm text-dark-600">
          ¿No tienes una cuenta?
          <router-link
            to="/register"
            class="font-medium text-primary-600 hover:text-primary-500 transition-colors duration-200"
          >
            Registrarse como desarrollador
          </router-link>
        </p>
      </div>

      <!-- Test Credentials -->
      <div class="bg-info-50 border border-info-200 text-info-700 px-4 py-3 rounded-lg text-sm">
        <p class="font-medium mb-1">Credenciales de prueba:</p>
        <p>Admin: admin@protasker.com / password</p>
        <p>Dev: dev@protasker.com / password</p>
      </div>
    </div>
  </div>
</template>