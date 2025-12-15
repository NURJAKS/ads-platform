import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { adsAPI } from '../api/ads'

const MyAds = () => {
  const [ads, setAds] = useState([])
  const [loading, setLoading] = useState(true)
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  useEffect(() => {
    loadAds()
  }, [pagination.current_page])

  const loadAds = async () => {
    setLoading(true)
    try {
      const response = await adsAPI.getMyAds()
      if (response.success) {
        setAds(response.data)
        setPagination(response.meta)
      }
    } catch (error) {
      console.error('Error loading ads:', error)
    } finally {
      setLoading(false)
    }
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

  const getImageUrl = (image) => {
    if (!image) return '/placeholder.jpg'
    // Используем url из API, если есть, иначе path
    return image.url || image.path || '/placeholder.jpg'
  }

  if (loading) {
    return <div className="loading">Загрузка...</div>
  }

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
        <h2 style={{ margin: 0 }}>Мои объявления ({pagination.total})</h2>
        <Link to="/ads/create" className="btn btn-primary">
          Создать новое объявление
        </Link>
      </div>

      {ads.length === 0 ? (
        <div className="card" style={{ textAlign: 'center', padding: '40px' }}>
          <p style={{ marginBottom: '20px', fontSize: '18px' }}>У вас пока нет объявлений</p>
          <Link to="/ads/create" className="btn btn-primary">
            Создать первое объявление
          </Link>
        </div>
      ) : (
        <div className="grid">
          {ads.map((ad) => (
            <div key={ad.id} className="ad-card">
              {ad.images && ad.images.length > 0 ? (
                <img src={getImageUrl(ad.images[0])} alt={ad.title} />
              ) : (
                <div style={{ height: '200px', background: '#f0f0f0', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                  Нет изображения
                </div>
              )}
              <div className="ad-card-content">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', marginBottom: '10px' }}>
                  <Link
                    to={`/ads/${ad.id}`}
                    style={{ textDecoration: 'none', color: 'inherit' }}
                  >
                    <div className="ad-card-title">{ad.title}</div>
                  </Link>
                  {getStatusBadge(ad.status)}
                </div>
                {ad.price && (
                  <div className="ad-card-price">{ad.price.toLocaleString()} ₽</div>
                )}
                <div className="ad-card-city">{ad.city}</div>
                {ad.category && (
                  <div style={{ marginTop: '10px', fontSize: '12px', color: '#999' }}>
                    {ad.category.name}
                  </div>
                )}
                <div style={{ marginTop: '15px', display: 'flex', gap: '10px' }}>
                  <Link
                    to={`/ads/${ad.id}/edit`}
                    className="btn btn-primary"
                    style={{ fontSize: '14px', padding: '5px 10px' }}
                  >
                    Редактировать
                  </Link>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}

export default MyAds

