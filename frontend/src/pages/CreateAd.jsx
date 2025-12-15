import React, { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { adsAPI } from '../api/ads'
import { categoriesAPI } from '../api/categories'

const CreateAd = () => {
  const navigate = useNavigate()
  const [categories, setCategories] = useState([])
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    category_id: '',
    price: '',
    city: '',
    images: [],
  })
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    loadCategories()
  }, [])

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

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const handleImageChange = (e) => {
    const files = Array.from(e.target.files)
    if (files.length > 10) {
      setError('Максимум 10 изображений')
      return
    }
    setFormData((prev) => ({ ...prev, images: files }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      const response = await adsAPI.create(formData)
      if (response.success) {
        navigate(`/ads/${response.data.id}`)
      }
    } catch (error) {
      const errorMessage = error.response?.data?.message || 'Ошибка при создании объявления'
      const errors = error.response?.data?.errors

      if (errors) {
        // Собираем все ошибки валидации в один текст
        const errorTexts = Object.entries(errors).flatMap(([field, messages]) =>
          Array.isArray(messages) ? messages : [messages]
        )
        setError(errorTexts.join(', ') || errorMessage)
        console.error('Validation errors:', errors)
      } else {
        setError(errorMessage)
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <div style={{ maxWidth: '800px', margin: '0 auto' }}>
      <div className="card">
        <h2>Создать объявление</h2>
        {error && <div className="alert alert-error">{error}</div>}
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

          <div className="form-group">
            <label>Изображения (макс. 5 МБ каждое)</label>
            <input
              type="file"
              multiple
              accept="image/*"
              onChange={handleImageChange}
            />
            {formData.images.length > 0 && (
              <div style={{ marginTop: '10px' }}>
                Выбрано файлов: {formData.images.length}
              </div>
            )}
          </div>

          <button type="submit" className="btn btn-primary" disabled={loading}>
            {loading ? 'Создание...' : 'Создать объявление'}
          </button>
        </form>
      </div>
    </div>
  )
}

export default CreateAd

