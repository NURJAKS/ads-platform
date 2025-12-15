import React, { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { adsAPI } from '../api/ads'
import { categoriesAPI } from '../api/categories'

const EditAd = () => {
  const { id } = useParams()
  const navigate = useNavigate()
  const [categories, setCategories] = useState([])
  const [ad, setAd] = useState(null)

  // Form State
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    category_id: '',
    price: '',
    city: '',
  })

  // Image State
  const [existingImages, setExistingImages] = useState([])
  const [deletedImageIds, setDeletedImageIds] = useState([])
  const [newImages, setNewImages] = useState([])

  const [error, setError] = useState('')
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    loadCategories()
    loadAd()
  }, [id])

  const loadCategories = async () => {
    try {
      const response = await categoriesAPI.getAll()
      if (response.success) {
        setCategories(response.data)
      }
    } catch (error) {
      console.error('Error loading categories:', error)
    }
  }

  const loadAd = async () => {
    try {
      const response = await adsAPI.getById(id)
      if (response.success) {
        const adData = response.data
        setAd(adData)
        setFormData({
          title: adData.title || '',
          description: adData.description || '',
          category_id: adData.category_id || '',
          price: adData.price || '',
          city: adData.city || '',
        })
        setExistingImages(adData.images || [])
      }
    } catch (error) {
      if (error.response?.status === 404) {
        setError('Объявление не найдено или у вас нет доступа к его редактированию')
      } else if (error.response?.status === 401) {
        setError('Необходима авторизация')
      } else {
        setError(error.response?.data?.message || 'Ошибка загрузки объявления')
      }
    } finally {
      setLoading(false)
    }
  }

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const handleDeleteExisting = (imageId) => {
    setExistingImages(prev => prev.filter(img => img.id !== imageId))
    setDeletedImageIds(prev => [...prev, imageId])
  }

  const handleNewImages = (e) => {
    const files = Array.from(e.target.files)

    // Check total count limit
    const totalCount = existingImages.length + files.length
    if (totalCount > 10) {
      setError(`Максимум 10 изображений. Сейчас: ${existingImages.length}, вы пытаетесь добавить: ${files.length}`)
      return
    }

    setNewImages(files)
    setError('') // Clear previous errors
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setSaving(true)

    const payload = {
      ...formData,
      deleted_images: deletedImageIds,
      new_images: newImages
    }

    try {
      const response = await adsAPI.update(id, payload)
      if (response.success) {
        navigate(`/ads/${id}`)
      }
    } catch (error) {
      setError(
        error.response?.data?.message || 'Ошибка при обновлении объявления'
      )
    } finally {
      setSaving(false)
    }
  }

  if (loading) {
    return <div className="loading">Загрузка...</div>
  }

  if (error && !ad) {
    return (
      <div className="card">
        <div className="alert alert-error">{error}</div>
        <button onClick={() => navigate('/my/ads')} className="btn btn-secondary">
          Вернуться к моим объявлениям
        </button>
      </div>
    )
  }

  return (
    <div style={{ maxWidth: '800px', margin: '0 auto' }}>
      <div className="card">
        <h2>Редактировать объявление</h2>
        {error && <div className="alert alert-error">{error}</div>}
        <div className="alert alert-info">
          После редактирования объявление будет отправлено на модерацию.
        </div>

        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Заголовок *</label>
            <input
              type="text"
              name="title"
              value={formData.title}
              onChange={handleChange}
              required
              maxLength={255}
            />
          </div>

          <div className="form-group">
            <label>Описание *</label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleChange}
              required
            />
          </div>

          <div className="form-group">
            <label>Категория *</label>
            <select
              name="category_id"
              value={formData.category_id}
              onChange={handleChange}
              required
            >
              <option value="">Выберите категорию</option>
              {categories.map((cat) => (
                <option key={cat.id} value={cat.id}>
                  {cat.name}
                </option>
              ))}
            </select>
          </div>

          <div className="form-group">
            <label>Цена</label>
            <input
              type="number"
              name="price"
              value={formData.price}
              onChange={handleChange}
              min="0"
              step="0.01"
            />
          </div>

          <div className="form-group">
            <label>Город *</label>
            <input
              type="text"
              name="city"
              value={formData.city}
              onChange={handleChange}
              required
              maxLength={150}
            />
          </div>

          {/* Image Management Section */}
          <div className="form-group">
            <label>Текущие изображения</label>
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: '10px', marginTop: '10px' }}>
              {existingImages.map(img => (
                <div key={img.id} style={{ position: 'relative', width: '100px', height: '100px' }}>
                  <img
                    src={img.url || img.path}
                    alt="ad"
                    style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '4px' }}
                  />
                  <button
                    type="button"
                    onClick={() => handleDeleteExisting(img.id)}
                    style={{
                      position: 'absolute', top: -5, right: -5,
                      background: 'red', color: 'white', border: 'none',
                      borderRadius: '50%', width: '20px', height: '20px',
                      cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center'
                    }}
                  >
                    &times;
                  </button>
                </div>
              ))}
              {existingImages.length === 0 && <p style={{ color: '#999' }}>Нет изображений</p>}
            </div>
          </div>

          <div className="form-group">
            <label>Добавить фото (Максимум 10 всего)</label>
            <input
              type="file"
              multiple
              accept="image/*"
              onChange={handleNewImages}
            />
            {newImages.length > 0 && (
              <div style={{ marginTop: '5px' }}>Выбрано новых: {newImages.length}</div>
            )}
          </div>

          <button type="submit" className="btn btn-primary" disabled={saving}>
            {saving ? 'Сохранение...' : 'Сохранить изменения'}
          </button>
        </form>
      </div>
    </div>
  )
}

export default EditAd
