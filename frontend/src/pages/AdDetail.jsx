import React, { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { adsAPI } from '../api/ads'
import { favoritesAPI } from '../api/favorites'
import { useAuth } from '../context/AuthContext'

const AdDetail = () => {
  const { id } = useParams()
  const navigate = useNavigate()
  const { isAuthenticated, user } = useAuth()
  const [ad, setAd] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [isFavorite, setIsFavorite] = useState(false)

  useEffect(() => {
    loadAd()
  }, [id])

  const loadAd = async () => {
    try {
      const response = await adsAPI.getById(id)
      if (response.success) {
        setAd(response.data)
      }
    } catch (error) {
      if (error.response?.status === 404) {
        setError('Объявление не найдено')
      } else {
        setError('Ошибка загрузки объявления')
      }
    } finally {
      setLoading(false)
    }
  }

  const handleDelete = async () => {
    if (!window.confirm('Вы уверены, что хотите удалить это объявление?')) {
      return
    }

    try {
      await adsAPI.delete(id)
      navigate('/my/ads')
    } catch (error) {
      alert('Ошибка при удалении объявления')
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

  if (loading) {
    return <div className="loading">Загрузка...</div>
  }

  if (error || !ad) {
    return (
      <div className="card">
        <div className="alert alert-error">{error || 'Объявление не найдено'}</div>
        <Link to="/" className="btn btn-secondary">Вернуться на главную</Link>
      </div>
    )
  }

  const canEdit = isAuthenticated && user?.id === ad.user_id
  const canDelete = canEdit

  return (
  const [showReportModal, setShowReportModal] = useState(false)
  const [reportReason, setReportReason] = useState('spam')
  const [reportComment, setReportComment] = useState('')
  const [reportMessage, setReportMessage] = useState('')

  const handleReportClick = () => {
    setShowReportModal(true)
    setReportMessage('')
  }

  const handleReportSubmit = async (e) => {
    e.preventDefault()
    try {
      await adsAPI.report(id, {
        reason: reportReason,
        comment: reportComment
      })
      setReportMessage('Жалоба успешно отправлена')
      setTimeout(() => setShowReportModal(false), 2000)
    } catch (err) {
      alert('Ошибка при отправке жалобы')
    }
  }

  return (
    <div>
      <div style={{ marginBottom: '20px' }}>
        <Link to="/" className="btn btn-secondary">← Назад</Link>
      </div>

      <div className="card">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', marginBottom: '20px' }}>
          <h1>{ad.title}</h1>
          {getStatusBadge(ad.status)}
        </div>

        {ad.images && ad.images.length > 0 && (
          <div style={{ marginBottom: '20px' }}>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '10px' }}>
              {ad.images.map((image, index) => (
                <img
                  key={index}
                  src={getImageUrl(image)}
                  alt={ad.title}
                  style={{ width: '100%', borderRadius: '8px' }}
                />
              ))}
            </div>
          </div>
        )}

        <div style={{ marginBottom: '20px' }}>
          <h3>Описание</h3>
          <p style={{ whiteSpace: 'pre-wrap' }}>{ad.description}</p>
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '15px', marginBottom: '20px' }}>
          {ad.price && (
            <div>
              <strong>Цена:</strong> {ad.price.toLocaleString()} ₽
            </div>
          )}
          <div>
            <strong>Город:</strong> {ad.city}
          </div>
          {ad.category && (
            <div>
              <strong>Категория:</strong> {ad.category.name}
            </div>
          )}
          {ad.user && (
            <div>
              <strong>Автор:</strong> {ad.user.name}
            </div>
          )}
        </div>

        <div style={{ display: 'flex', gap: '10px', marginTop: '20px' }}>
          {canEdit ? (
            <>
              <Link to={`/ads/${id}/edit`} className="btn btn-primary">
                Редактировать
              </Link>
              {canDelete && (
                <button onClick={handleDelete} className="btn btn-danger">
                  Удалить
                </button>
              )}
            </>
          ) : isAuthenticated ? (
            <button onClick={handleReportClick} className="btn btn-secondary" style={{ backgroundColor: '#f0ad4e', color: 'white', border: 'none' }}>
              Пожаловаться
            </button>
          ) : null}
        </div>
      </div>

      {/* Report Modal */}
      {showReportModal && (
        <div style={{
          position: 'fixed', top: 0, left: 0, right: 0, bottom: 0,
          backgroundColor: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center'
        }}>
          <div className="card" style={{ width: '400px', maxWidth: '90%' }}>
            <h3>Пожаловаться на объявление</h3>
            {reportMessage ? (
              <div className="alert alert-success">{reportMessage}</div>
            ) : (
              <form onSubmit={handleReportSubmit}>
                <div className="form-group">
                  <label>Причина</label>
                  <select
                    value={reportReason}
                    onChange={e => setReportReason(e.target.value)}
                    style={{ width: '100%', padding: '8px' }}
                  >
                    <option value="spam">Спам</option>
                    <option value="fraud">Мошенничество</option>
                    <option value="inappropriate">Неприемлемый контент</option>
                    <option value="other">Другое</option>
                  </select>
                </div>
                <div className="form-group">
                  <label>Комментарий</label>
                  <textarea
                    value={reportComment}
                    onChange={e => setReportComment(e.target.value)}
                    style={{ width: '100%', height: '80px', padding: '8px' }}
                  />
                </div>
                <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end' }}>
                  <button type="button" className="btn btn-secondary" onClick={() => setShowReportModal(false)}>
                    Отмена
                  </button>
                  <button type="submit" className="btn btn-primary">
                    Отправить
                  </button>
                </div>
              </form>
            )}
          </div>
        </div>
      )}
    </div>
  )
}

export default AdDetail

