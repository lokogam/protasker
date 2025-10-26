<script setup>
import { ref } from 'vue'
import { useAuth } from '../composables/useAuth.js'
import { useRouter } from 'vue-router'
import AppInput from '../components/common/AppInput.vue'
import AppButton from '../components/common/AppButton.vue'

const { register, loading, error } = useAuth()
const router = useRouter()

// Estado del formulario
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

// Errores de validación
const errors = ref({})

// Función para limpiar errores
const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

// Función para manejar el envío del formulario
const handleSubmit = async () => {
  // Limpiar errores previos
  errors.value = {}

  // Validaciones básicas del frontend
  if (!form.value.name.trim()) {
    errors.value.name = ['El nombre es requerido']
    return
  }

  if (!form.value.email.trim()) {
    errors.value.email = ['El email es requerido']
    return
  }

  if (!form.value.password) {
    errors.value.password = ['La contraseña es requerida']
    return
  }

  if (form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = ['Las contraseñas no coinciden']
    return
  }

  if (form.value.password.length < 8) {
    errors.value.password = ['La contraseña debe tener al menos 8 caracteres']
    return
  }

  try {
    const result = await register(
      form.value.name,
      form.value.email,
      form.value.password,
      form.value.password_confirmation
    )

    if (result.success) {
      // Registro exitoso, redirigir al dashboard
      router.push('/dashboard/developer')
    } else {
      // Mostrar errores del servidor
      if (result.errors) {
        errors.value = result.errors
      }
    }
  } catch (err) {
    console.error('Error en registro:', err)
  }
}

// Función para ir al login
const goToLogin = () => {
  router.push('/login')
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-16 w-16 bg-primary-600 rounded-full flex items-center justify-center mb-6">
          <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-dark-900">Registrarse en Protasker</h2>
        <p class="mt-2 text-sm text-dark-600">
          Crea tu cuenta como desarrollador
        </p>
      </div>

      <!-- Formulario -->
      <div class="bg-white rounded-lg shadow-lg p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Mensaje de error general -->
          <div v-if="error" class="bg-danger-50 border border-danger-200 rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-danger-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-danger-800">{{ error }}</p>
              </div>
            </div>
          </div>

          <!-- Nombre completo -->
          <AppInput
            v-model="form.name"
            type="text"
            label="Nombre completo"
            placeholder="Ingresa tu nombre completo"
            :error="errors.name?.[0]"
            required
            @input="clearError('name')"
          />

          <!-- Email -->
          <AppInput
            v-model="form.email"
            type="email"
            label="Correo electrónico"
            placeholder="correo@ejemplo.com"
            :error="errors.email?.[0]"
            required
            @input="clearError('email')"
          />

          <!-- Contraseña -->
          <AppInput
            v-model="form.password"
            type="password"
            label="Contraseña"
            placeholder="Mínimo 8 caracteres"
            :error="errors.password?.[0]"
            required
            @input="clearError('password')"
          />

          <!-- Confirmar contraseña -->
          <AppInput
            v-model="form.password_confirmation"
            type="password"
            label="Confirmar contraseña"
            placeholder="Repite tu contraseña"
            :error="errors.password_confirmation?.[0]"
            required
            @input="clearError('password_confirmation')"
          />

          <!-- Información sobre el rol -->
          <div class="bg-info-50 border border-info-200 rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-info-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-info-800">
                  <strong>Nota:</strong> Tu cuenta será creada con rol de Desarrollador.
                  Podrás gestionar tareas y proyectos asignados a ti.
                </p>
              </div>
            </div>
          </div>

          <!-- Botón de registro -->
          <AppButton
            type="submit"
            variant="primary"
            size="lg"
            :loading="loading"
            :disabled="loading"
            class="w-full"
          >
            {{ loading ? 'Registrando...' : 'Crear cuenta' }}
          </AppButton>
        </form>

        <!-- Link para ir al login -->
        <div class="mt-6 text-center">
          <p class="text-sm text-dark-600">
            ¿Ya tienes una cuenta?
            <button
              @click="goToLogin"
              class="font-medium text-primary-600 hover:text-primary-500 transition-colors duration-200"
            >
              Iniciar sesión
            </button>
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center">
        <p class="text-xs text-dark-500">
          © {{ new Date().getFullYear() }} Protasker. Sistema de gestión de proyectos y tareas.
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animaciones suaves para el formulario */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>