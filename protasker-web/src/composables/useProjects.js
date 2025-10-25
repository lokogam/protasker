import { ref, computed } from 'vue'
import { projectService } from '../services/projects.js'

// Estado global reactivo para proyectos
const projects = ref([])
const currentProject = ref(null)

export function useProjects() {
  const loading = ref(false)
  const error = ref('')

  // Obtener todos los proyectos
  const fetchProjects = async (filters = {}) => {
    loading.value = true
    error.value = ''

    try {
      const result = await projectService.getProjects(filters)
      
      if (result.success) {
        // Si la respuesta tiene paginación, extraer los datos
        if (result.data.data) {
          projects.value = result.data.data
        } else {
          projects.value = result.data
        }
        return true
      } else {
        error.value = result.message
        return false
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener proyectos'
      return false
    } finally {
      loading.value = false
    }
  }

  // Obtener un proyecto específico
  const fetchProject = async (id) => {
    loading.value = true
    error.value = ''

    try {
      const result = await projectService.getProject(id)
      
      if (result.success) {
        currentProject.value = result.data.data || result.data
        return currentProject.value
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener proyecto'
      return null
    } finally {
      loading.value = false
    }
  }

  // Crear nuevo proyecto
  const createProject = async (projectData) => {
    loading.value = true
    error.value = ''

    try {
      const result = await projectService.createProject(projectData)
      
      if (result.success) {
        const newProject = result.data.data || result.data
        projects.value.unshift(newProject)
        return newProject
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al crear proyecto'
      return null
    } finally {
      loading.value = false
    }
  }

  // Actualizar proyecto
  const updateProject = async (id, projectData) => {
    loading.value = true
    error.value = ''

    try {
      const result = await projectService.updateProject(id, projectData)
      
      if (result.success) {
        const updatedProject = result.data.data || result.data
        
        // Actualizar en la lista
        const index = projects.value.findIndex(p => p.id === id)
        if (index !== -1) {
          projects.value[index] = updatedProject
        }
        
        // Actualizar proyecto actual si coincide
        if (currentProject.value?.id === id) {
          currentProject.value = updatedProject
        }
        
        return updatedProject
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al actualizar proyecto'
      return null
    } finally {
      loading.value = false
    }
  }

  // Eliminar proyecto
  const deleteProject = async (id) => {
    loading.value = true
    error.value = ''

    try {
      const result = await projectService.deleteProject(id)
      
      if (result.success) {
        // Remover de la lista
        projects.value = projects.value.filter(p => p.id !== id)
        
        // Limpiar proyecto actual si es el mismo
        if (currentProject.value?.id === id) {
          currentProject.value = null
        }
        
        return true
      } else {
        error.value = result.message
        return false
      }
    } catch (err) {
      error.value = 'Error inesperado al eliminar proyecto'
      return false
    } finally {
      loading.value = false
    }
  }

  // Limpiar estado
  const clearProjects = () => {
    projects.value = []
    currentProject.value = null
    error.value = ''
  }

  return {
    // Estado
    projects: computed(() => projects.value),
    currentProject: computed(() => currentProject.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    
    // Acciones
    fetchProjects,
    fetchProject,
    createProject,
    updateProject,
    deleteProject,
    clearProjects
  }
}