import api from './api.js'

// Servicio de proyectos
export const projectService = {
  // Obtener todos los proyectos (con filtros opcionales)
  async getProjects(filters = {}) {
    try {
      const params = new URLSearchParams(filters).toString()
      const url = params ? `/projects?${params}` : '/projects'
      const response = await api.get(url)
      return { success: true, data: response.data }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener proyectos'
      }
    }
  },

  // Obtener un proyecto específico
  async getProject(id) {
    try {
      const response = await api.get(`/projects/${id}`)
      return { success: true, data: response.data }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al obtener proyecto'
      }
    }
  },

  // Crear nuevo proyecto
  async createProject(projectData) {
    try {
      const response = await api.post('/projects', projectData)
      return { success: true, data: response.data }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al crear proyecto',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  // Actualizar proyecto
  async updateProject(id, projectData) {
    try {
      const response = await api.put(`/projects/${id}`, projectData)
      return { success: true, data: response.data }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al actualizar proyecto',
        errors: error.response?.data?.errors || {}
      }
    }
  },

  // Eliminar proyecto
  async deleteProject(id) {
    try {
      const response = await api.delete(`/projects/${id}`)
      return { success: true, message: response.data.message }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Error al eliminar proyecto'
      }
    }
  }
}