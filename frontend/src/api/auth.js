import apiClient from './client'

export const authAPI = {
  register: async (data) => {
    const response = await apiClient.post('/auth/register', data)
    return response.data
  },

  login: async (email, password) => {
    const response = await apiClient.post('/auth/login', { email, password })
    return response.data
  },

  logout: async () => {
    const response = await apiClient.post('/auth/logout')
    return response.data
  },
}

