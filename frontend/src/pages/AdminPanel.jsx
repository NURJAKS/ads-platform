import React, { useState, useEffect } from 'react'
import { adminAPI } from '../api/admin'

const AdminPanel = () => {
  const [ads, setAds] = useState([])
  const [logs, setLogs] = useState([])
  const [status, setStatus] = useState('pending')
  const [loading, setLoading] = useState(true)
  const [activeTab, setActiveTab] = useState('ads')
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    total: 0,
  })
  const [rejectReason, setRejectReason] = useState('')
  const [rejectingAdId, setRejectingAdId] = useState(null)

  useEffect(() => {
    if (activeTab === 'ads') {
      loadAds()
    } else {
      loadLogs()
    }
  }, [status, activeTab, pagination.current_page])

  const loadAds = async () => {
    setLoading(true)
    try {
      const response = await adminAPI.getAds(status)
      if (response.success) {
        // Laravel paginate() wraps items in 'data' key
        setAds(response.data.data)
        setPagination(response.data)
      }
    } catch (error) {
      console.error('Error loading ads:', error)
    } finally {
      setLoading(false)
    }
  }

  const loadLogs = async () => {
    setLoading(true)
    try {
      const response = await adminAPI.getModerationLogs()
      if (response.success) {
        setLogs(response.data.data)
        setPagination(response.data)
      }
    } catch (error) {
      console.error('Error loading logs:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleApprove = async (id) => {
    if (!window.confirm('Одобрить это объявление?')) {
      return
    }

    try {
      const response = await adminAPI.approveAd(id)
      if (response.success) {
        loadAds()
      }
    } catch (error) {
      alert(error.response?.data?.message || 'Ошибка при одобрении')
    }
  }

  const handleReject = async (id) => {
    if (!rejectReason.trim()) {
      alert('Укажите причину отклонения')
      return
    }

    try {
      const response = await adminAPI.rejectAd(id, rejectReason)
      if (response.success) {
        setRejectReason('')
        setRejectingAdId(null)
        loadAds()
      }
    } catch (error) {
      alert(error.response?.data?.message || 'Ошибка при отклонении')
    }
  }

  const getImageUrl = (image) => {
    if (!image) return '/placeholder.jpg'
    // Используем url из API, если есть, иначе path
    return image.url || image.path || '/placeholder.jpg'
  }

  const getStatusBadge = (status) => {
    const badges = {
      pending: { class: 'badge-pending', text: 'На модерации' },
      approved: { class: 'badge-approved', text: 'Одобрено' },
      rejected: { class: 'badge-rejected', text: 'Отклонено' },
    }
    const badge = badges[status] || badges.pending
    return <span className={`badge ${badge.class}`}>{badge.text}</span>
  }

  return (
    <div>
      <h2>Админ-панель</h2>

      <div style={{ display: 'flex', gap: '10px', marginBottom: '20px' }}>
        <button
          className={`btn ${activeTab === 'ads' ? 'btn-primary' : 'btn-secondary'}`}
          onClick={() => setActiveTab('ads')}
        >
          Объявления
        </button>
        <button
          className={`btn ${activeTab === 'logs' ? 'btn-primary' : 'btn-secondary'}`}
          onClick={() => setActiveTab('logs')}
        >
          История модерации
        </button>
      </div>

      {activeTab === 'ads' && (
        <>
          <div style={{ display: 'flex', gap: '10px', marginBottom: '20px' }}>
            <button
              className={`btn ${status === 'pending' ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setStatus('pending')}
            >
              На модерации ({pagination.total})
            </button>
            <button
              className={`btn ${status === 'approved' ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setStatus('approved')}
            >
              Одобренные
            </button>
            <button
              className={`btn ${status === 'rejected' ? 'btn-primary' : 'btn-secondary'}`}
              onClick={() => setStatus('rejected')}
            >
              Отклоненные
            </button>
          </div>

          {loading ? (
            <div className="loading">Загрузка...</div>
          ) : ads.length === 0 ? (
            <div className="card">
              <p>Нет объявлений со статусом "{status}"</p>
            </div>
          ) : (
            <div>
              {ads.map((ad) => (
                <div key={ad.id} className="card">
                  <div style={{ display: 'flex', gap: '20px' }}>
                    {ad.images && ad.images.length > 0 && (
                      <img
                        src={getImageUrl(ad.images[0])}
                        alt={ad.title}
                        style={{ width: '200px', height: '200px', objectFit: 'cover', borderRadius: '8px' }}
                      />
                    )}
                    <div style={{ flex: 1 }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', marginBottom: '10px' }}>
                        <h3>{ad.title}</h3>
                        {getStatusBadge(ad.status)}
                      </div>
                      <p style={{ marginBottom: '10px', whiteSpace: 'pre-wrap' }}>{ad.description}</p>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))', gap: '10px', marginBottom: '15px' }}>
                        {ad.price && <div><strong>Цена:</strong> {ad.price.toLocaleString()} ₽</div>}
                        <div><strong>Город:</strong> {ad.city}</div>
                        {ad.category && <div><strong>Категория:</strong> {ad.category.name}</div>}
                        {ad.user && <div><strong>Автор:</strong> {ad.user.name}</div>}
                      </div>
                      {ad.status === 'pending' && (
                        <div style={{ display: 'flex', gap: '10px' }}>
                          <button
                            className="btn btn-success"
                            onClick={() => handleApprove(ad.id)}
                          >
                            Одобрить
                          </button>
                          <button
                            className="btn btn-danger"
                            onClick={() => setRejectingAdId(ad.id)}
                          >
                            Отклонить
                          </button>
                        </div>
                      )}
                      {rejectingAdId === ad.id && (
                        <div style={{ marginTop: '15px', padding: '15px', background: '#f8f9fa', borderRadius: '5px' }}>
                          <div className="form-group">
                            <label>Причина отклонения *</label>
                            <textarea
                              value={rejectReason}
                              onChange={(e) => setRejectReason(e.target.value)}
                              placeholder="Укажите причину отклонения объявления"
                              rows={3}
                            />
                          </div>
                          <div style={{ display: 'flex', gap: '10px' }}>
                            <button
                              className="btn btn-danger"
                              onClick={() => handleReject(ad.id)}
                            >
                              Отклонить
                            </button>
                            <button
                              className="btn btn-secondary"
                              onClick={() => {
                                setRejectingAdId(null)
                                setRejectReason('')
                              }}
                            >
                              Отмена
                            </button>
                          </div>
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </>
      )}

      {activeTab === 'logs' && (
        <>
          {loading ? (
            <div className="loading">Загрузка...</div>
          ) : logs.length === 0 ? (
            <div className="card">
              <p>История модерации пуста</p>
            </div>
          ) : (
            <div>
              {logs.map((log) => (
                <div key={log.id} className="card">
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', marginBottom: '10px' }}>
                    <div>
                      <strong>Объявление:</strong> {log.ad?.title || 'N/A'}
                    </div>
                    <div>
                      {getStatusBadge(log.old_status)} → {getStatusBadge(log.new_status)}
                    </div>
                  </div>
                  <div style={{ marginBottom: '10px' }}>
                    <strong>Администратор:</strong> {log.admin?.name || 'N/A'}
                  </div>
                  {log.comment && (
                    <div style={{ marginBottom: '10px' }}>
                      <strong>Комментарий:</strong> {log.comment}
                    </div>
                  )}
                  <div style={{ fontSize: '14px', color: '#666' }}>
                    {new Date(log.created_at).toLocaleString('ru-RU')}
                  </div>
                </div>
              ))}
            </div>
          )}
        </>
      )}
    </div>
  )
}

export default AdminPanel

