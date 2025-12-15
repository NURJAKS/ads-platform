import apiClient from './client'

export const adminAPI = {
  getAds: async (status = 'pending') => {
    const response = await apiClient.get('/admin/ads', { params: { status } })
    return response.data
  },

  approveAd: async (id) => {
    const response = await apiClient.post(`/admin/ads/${id}/approve`)
    return response.data
  },

  rejectAd: async (id, reason) => {
    const response = await apiClient.post(`/admin/ads/${id}/reject`, { reason })
    return response.data
  },

  getModerationLogs: async () => {
    const response = await apiClient.get('/admin/moderation/logs')
    return response.data
  },
}

