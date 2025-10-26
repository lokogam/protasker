import api from './api.js'

export const userService = {
  // Obtener todos los usuarios
  async getUsers() {
    try {
      const response = await api.get('/users')
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching users:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener usuarios' 
      }
    }
  },

  // Obtener un usuario específico
  async getUser(id) {
    try {
      const response = await api.get(`/users/${id}`)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching user:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener usuario' 
      }
    }
  },

  // Buscar usuarios por nombre o email
  async searchUsers(query = '', limit = 10) {
    try {
      const response = await api.get('/users/search', {
        params: { 
          query,
          limit 
        }
      })
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error searching users:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al buscar usuarios' 
      }
    }
  },

  // Obtener usuarios desarrolladores
  async getDevelopers() {
    try {
      const response = await api.get('/users?role=desarrollador')
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching developers:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener desarrolladores' 
      }
    }
  }
}