import { ref, computed } from 'vue'
import { taskService } from '../services/tasks.js'

// Estado global reactivo para tareas
const tasks = ref([])
const users = ref([]) // Para asignación de tareas
const loading = ref(false)
const error = ref('')

export function useTasks() {
  // Computed para estadísticas de tareas
  const taskStats = computed(() => {
    return {
      total: tasks.value.length,
      pending: tasks.value.filter(task => task.status === 'pending').length,
      in_progress: tasks.value.filter(task => task.status === 'in_progress').length,
      completed: tasks.value.filter(task => task.status === 'completed').length
    }
  })

  // Obtener todas las tareas con filtros
  const fetchTasks = async (filters = {}) => {
    loading.value = true
    error.value = ''

    try {
      const result = await taskService.getTasks(filters)
      
      if (result.success) {
        tasks.value = result.data.data || result.data // Manejar paginación
        return result.data
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener tareas'
      console.error('Error fetching tasks:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  // Obtener tareas de un proyecto específico
  const fetchProjectTasks = async (projectId) => {
    loading.value = true
    error.value = ''

    try {
      const result = await taskService.getProjectTasks(projectId)
      
      if (result.success) {
        tasks.value = result.data.data || result.data
        return result.data
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener tareas del proyecto'
      console.error('Error fetching project tasks:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  // Obtener tareas de un usuario
  const fetchUserTasks = async (userId) => {
    loading.value = true
    error.value = ''

    try {
      const result = await taskService.getUserTasks(userId)
      
      if (result.success) {
        tasks.value = result.data.data || result.data
        return result.data
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener tareas del usuario'
      console.error('Error fetching user tasks:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  // Obtener una tarea específica
  const fetchTask = async (id) => {
    loading.value = true
    error.value = ''

    try {
      const result = await taskService.getTask(id)
      
      if (result.success) {
        return result.data
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener tarea'
      console.error('Error fetching task:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  // Crear nueva tarea
  const createTask = async (taskData) => {
    loading.value = true
    error.value = ''

    try {
      const result = await taskService.createTask(taskData)
      
      if (result.success) {
        // Agregar la nueva tarea al estado local
        tasks.value.unshift(result.data)
        return result.data
      } else {
        error.value = result.message
        throw new Error(result.message)
      }
    } catch (err) {
      error.value = err.message || 'Error inesperado al crear tarea'
      console.error('Error creating task:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Actualizar tarea
  const updateTask = async (id, taskData) => {
    loading.value = true
    error.value = ''

    try {
      const result = await taskService.updateTask(id, taskData)
      
      if (result.success) {
        // Actualizar la tarea en el estado local
        const index = tasks.value.findIndex(task => task.id === id)
        if (index !== -1) {
          tasks.value[index] = result.data
        }
        return result.data
      } else {
        error.value = result.message
        throw new Error(result.message)
      }
    } catch (err) {
      error.value = err.message || 'Error inesperado al actualizar tarea'
      console.error('Error updating task:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Eliminar tarea
  const deleteTask = async (id) => {
    loading.value = true
    error.value = ''

    try {
      const result = await taskService.deleteTask(id)
      
      if (result.success) {
        // Remover la tarea del estado local
        tasks.value = tasks.value.filter(task => task.id !== id)
        return true
      } else {
        error.value = result.message
        throw new Error(result.message)
      }
    } catch (err) {
      error.value = err.message || 'Error inesperado al eliminar tarea'
      console.error('Error deleting task:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Obtener usuarios disponibles para asignación
  const fetchAvailableUsers = async () => {
    try {
      const result = await taskService.getAvailableUsers()
      
      if (result.success) {
        users.value = result.data
        return result.data
      } else {
        console.error('Error fetching users:', result.message)
        return []
      }
    } catch (err) {
      console.error('Error fetching users:', err)
      return []
    }
  }

  // Filtrar tareas por estado
  const getTasksByStatus = (status) => {
    return computed(() => tasks.value.filter(task => task.status === status))
  }

  // Filtrar tareas por usuario asignado
  const getTasksByUser = (userId) => {
    return computed(() => tasks.value.filter(task => task.assigned_to === userId))
  }

  return {
    // Estado
    tasks: computed(() => tasks.value),
    users: computed(() => users.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    taskStats,

    // Acciones
    fetchTasks,
    fetchProjectTasks,
    fetchUserTasks,
    fetchTask,
    createTask,
    updateTask,
    deleteTask,
    fetchAvailableUsers,

    // Utilidades
    getTasksByStatus,
    getTasksByUser,

    // Limpiar estado
    clearError: () => { error.value = '' },
    clearTasks: () => { tasks.value = [] }
  }
}