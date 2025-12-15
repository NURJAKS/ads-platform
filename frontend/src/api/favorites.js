import apiClient from './client'

export const favoritesAPI = {
  add: async (adId) => {
    const response = await apiClient.post(`/ads/${adId}/favorite`)
    return response.data
  },

  remove: async (adId) => {
    const response = await apiClient.delete(`/ads/${adId}/favorite`)
    return response.data
  },

  getAll: async () => {
    const response = await apiClient.get('/favorites')
    return response.data
  },
}

