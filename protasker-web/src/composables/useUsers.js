import { ref, computed } from 'vue'
import { userService } from '../services/users.js'

export function useUsers() {
  const users = ref([])
  const developers = ref([])
  const loading = ref(false)
  const error = ref('')

  // Computed para separar usuarios por rol
  const administrators = computed(() => 
    users.value.filter(user => 
      user.roles?.some(role => role.name === 'administrador')
    )
  )

  // Obtener todos los usuarios
  const fetchUsers = async () => {
    loading.value = true
    error.value = ''

    try {
      const result = await userService.getUsers()
      
      if (result.success) {
        users.value = result.data.data || result.data
        return result.data
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener usuarios'
      console.error('Error fetching users:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  // Obtener desarrolladores
  const fetchDevelopers = async () => {
    loading.value = true
    error.value = ''

    try {
      const result = await userService.getDevelopers()
      
      if (result.success) {
        developers.value = result.data.data || result.data
        return result.data
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener desarrolladores'
      console.error('Error fetching developers:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  // Obtener un usuario específico
  const fetchUser = async (id) => {
    loading.value = true
    error.value = ''

    try {
      const result = await userService.getUser(id)
      
      if (result.success) {
        return result.data
      } else {
        error.value = result.message
        return null
      }
    } catch (err) {
      error.value = 'Error inesperado al obtener usuario'
      console.error('Error fetching user:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  // Buscar usuarios por nombre o email
  const searchUsers = async (query = '', limit = 10) => {
    loading.value = true
    error.value = ''

    try {
      const result = await userService.searchUsers(query, limit)
      
      if (result.success) {
        return result.data.data || result.data
      } else {
        error.value = result.message
        return []
      }
    } catch (err) {
      error.value = 'Error inesperado al buscar usuarios'
      console.error('Error searching users:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  // Obtener usuario por ID (con cache)
  const getUser = async (id) => {
    if (!id) return null
    
    // Buscar primero en cache
    const cachedUser = users.value.find(user => user.id.toString() === id.toString()) ||
                      developers.value.find(user => user.id.toString() === id.toString())
    if (cachedUser) {
      return cachedUser
    }

    // Si no está en cache, buscar en API
    return await fetchUser(id)
  }

  return {
    users,
    developers,
    loading,
    error,
    administrators,
    fetchUsers,
    fetchDevelopers,
    fetchUser,
    searchUsers,
    getUser
  }
}