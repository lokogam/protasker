import api from './api.js'

// Servicio de tareas
export const taskService = {
  // Obtener todas las tareas con filtros
  async getTasks(filters = {}) {
    try {
      const params = new URLSearchParams()
      
      if (filters.project_id) params.append('project_id', filters.project_id)
      if (filters.status) params.append('status', filters.status)
      if (filters.assigned_to) params.append('assigned_to', filters.assigned_to)
      if (filters.page) params.append('page', filters.page)

      const response = await api.get(`/tasks?${params.toString()}`)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching tasks:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener tareas' 
      }
    }
  },

  // Obtener tareas de un proyecto específico
  async getProjectTasks(projectId) {
    try {
      const response = await api.get(`/tasks?project_id=${projectId}`)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching project tasks:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener tareas del proyecto' 
      }
    }
  },

  // Obtener tareas asignadas a un usuario
  async getUserTasks(userId) {
    try {
      const response = await api.get(`/tasks?assigned_to=${userId}`)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching user tasks:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener tareas del usuario' 
      }
    }
  },

  // Obtener una tarea específica
  async getTask(id) {
    try {
      const response = await api.get(`/tasks/${id}`)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching task:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener tarea' 
      }
    }
  },

  // Crear nueva tarea
  async createTask(taskData) {
    try {
      const response = await api.post('/tasks', taskData)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error creating task:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al crear tarea',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  // Actualizar tarea
  async updateTask(id, taskData) {
    try {
      const response = await api.put(`/tasks/${id}`, taskData)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error updating task:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al actualizar tarea',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  // Eliminar tarea
  async deleteTask(id) {
    try {
      await api.delete(`/tasks/${id}`)
      return { success: true }
    } catch (error) {
      console.error('Error deleting task:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al eliminar tarea' 
      }
    }
  },

  // Obtener usuarios disponibles para asignación
  async getAvailableUsers() {
    try {
      const response = await api.get('/users') // Asumiendo que existe este endpoint
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Error fetching users:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener usuarios' 
      }
    }
  }
}
