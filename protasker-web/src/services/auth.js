import api from './api.js'

// Servicio de autenticación
export const authService = {
  // Login del usuario
  async login(email, password) {
    try {
      const response = await api.post('/login', { email, password })
      const { token, user } = response.data

      // Guardar token y usuario en localStorage
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))

      return { success: true, data: { token, user } }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Error en el login'
      }
    }
  },

  // Registro de nuevo usuario
  async register(name, email, password, passwordConfirmation) {
    try {
      const response = await api.post('/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      })
      const { token, user } = response.data

      // Guardar token y usuario en localStorage
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))

      return { success: true, data: { token, user } }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Error en el registro',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  // Logout del usuario
  logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  },

  // Obtener usuario actual del localStorage
  getCurrentUser() {
    const user = localStorage.getItem('user')
    return user ? JSON.parse(user) : null
  },

  // Obtener usuario actual desde la API (con roles actualizados)
  async getUser() {
    try {
      const response = await api.get('/user')
      const user = response.data

      // Actualizar usuario en localStorage
      localStorage.setItem('user', JSON.stringify(user))

      return user
    } catch (error) {
      console.error('Error getting user:', error)
      // Si hay error, limpiar datos de autenticación
      this.logout()
      throw error
    }
  },

  // Verificar si está autenticado
  isAuthenticated() {
    return !!localStorage.getItem('token')
  }
}