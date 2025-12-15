import apiClient from './client'

export const adsAPI = {
  getAll: async (params = {}) => {
    const response = await apiClient.get('/ads', { params })
    return response.data
  },

  getById: async (id) => {
    const response = await apiClient.get(`/ads/${id}`)
    return response.data
  },

  create: async (data) => {
    const formData = new FormData()
    formData.append('title', data.title)
    formData.append('description', data.description)
    formData.append('category_id', data.category_id)
    formData.append('price', data.price || '')
    formData.append('city', data.city)

    if (data.images && data.images.length > 0) {
      // Laravel ожидает массив файлов - используем images[] для массива
      data.images.forEach((image) => {
        formData.append('images[]', image)
      })
    }

    // Axios автоматически установит правильный Content-Type для FormData
    const response = await apiClient.post('/ads', formData)
    return response.data
  },

  update: async (id, data) => {
    const formData = new FormData()
    formData.append('_method', 'PUT') // Method spoofing for Laravel
    formData.append('title', data.title)
    formData.append('description', data.description)
    formData.append('category_id', data.category_id)
    formData.append('price', data.price || '')
    formData.append('city', data.city)

    // Handle new images
    if (data.new_images && data.new_images.length > 0) {
      data.new_images.forEach((image) => {
        formData.append('new_images[]', image)
      })
    }

    // Handle deleted images
    if (data.deleted_images && data.deleted_images.length > 0) {
      data.deleted_images.forEach((id) => {
        formData.append('deleted_images[]', id)
      })
    }

    const response = await apiClient.post(`/ads/${id}`, formData)
    return response.data
  },

  delete: async (id) => {
    const response = await apiClient.delete(`/ads/${id}`)
    return response.data
  },

  getMyAds: async () => {
    const response = await apiClient.get('/my/ads')
    return response.data
  },

  report: async (id, data) => {
    const response = await apiClient.post(`/ads/${id}/complaint`, data)
    return response.data
  },
}

