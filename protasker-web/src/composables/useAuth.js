import { ref, computed } from 'vue'
import { authService } from '../services/auth.js'

// Estado global reactivo
const user = ref(authService.getCurrentUser())
const isLoggedIn = computed(() => !!user.value)

export function useAuth() {
  const loading = ref(false)
  const error = ref('')

  // Función para hacer login
  const login = async (email, password) => {
    loading.value = true
    error.value = ''

    try {
      const result = await authService.login(email, password)
      
      if (result.success) {
        user.value = result.data.user
        return true
      } else {
        error.value = result.message
        return false
      }
    } catch (err) {
      error.value = 'Error inesperado'
      return false
    } finally {
      loading.value = false
    }
  }

  // Función para hacer logout
  const logout = () => {
    authService.logout()
    user.value = null
  }

  // Función para obtener usuario actual (desde API)
  const getUser = async () => {
    try {
      const userData = await authService.getUser()
      user.value = userData
      return userData
    } catch (error) {
      console.error('Error getting user:', error)
      user.value = null
      throw error
    }
  }

  return {
    user: computed(() => user.value),
    isLoggedIn,
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    login,
    logout,
    getUser
  }
}