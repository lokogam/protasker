import apiClient from './apiClient'

export const userService = {
  /**
   * Obtener todos los usuarios con búsqueda opcional
   */
  async getUsers(params = {}) {
    const response = await apiClient.get('/users', { params })
    return response.data
  },

  /**
   * Buscar usuarios por nombre o email
   */
  async searchUsers(query = '', limit = 10) {
    const response = await apiClient.get('/users/search', {
      params: { 
        query,
        limit 
      }
    })
    return response.data
  },

  /**
   * Obtener un usuario por ID
   */
  async getUser(id) {
    const response = await apiClient.get(`/users/${id}`)
    return response.data
  },

  /**
   * Obtener usuarios por rol
   */
  async getUsersByRole(role) {
    const response = await apiClient.get('/users', {
      params: { role }
    })
    return response.data
  },

  /**
   * Obtener solo desarrolladores (para asignación de tareas)
   */
  async getDevelopers() {
    return this.getUsersByRole('desarrollador')
  }
}

export default userService