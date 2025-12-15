import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { adsAPI } from '../api/ads'
import { categoriesAPI } from '../api/categories'

const Home = () => {
  const [ads, setAds] = useState([])
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(true)
  const [filters, setFilters] = useState({
    category: '',
    city: '',
    min_price: '',
    max_price: '',
    search: '',
  })
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  useEffect(() => {
    loadCategories()
  }, [])

  useEffect(() => {
    loadAds()
  }, [filters, pagination.current_page])

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

  const loadAds = async () => {
    setLoading(true)
    try {
      const params = {
        page: pagination.current_page,
        ...Object.fromEntries(
          Object.entries(filters).filter(([_, v]) => v !== '')
        ),
      }
      const response = await adsAPI.getAll(params)
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

  const handleFilterChange = (e) => {
    const { name, value } = e.target
    setFilters((prev) => ({ ...prev, [name]: value }))
    setPagination((prev) => ({ ...prev, current_page: 1 }))
  }

  const handlePageChange = (page) => {
    setPagination((prev) => ({ ...prev, current_page: page }))
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const getImageUrl = (image) => {
    if (!image) return '/placeholder.jpg'
    // Используем url из API, если есть, иначе path
    return image.url || image.path || '/placeholder.jpg'
  }

  if (loading && ads.length === 0) {
    return <div className="loading">Загрузка объявлений...</div>
  }

  return (
    <div>
      <div className="card">
        <h2>Фильтры</h2>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '15px' }}>
          <div className="form-group">
            <label>Категория</label>
            <select name="category" value={filters.category} onChange={handleFilterChange}>
              <option value="">Все категории</option>
              {categories.map((cat) => (
                <option key={cat.id} value={cat.id}>
                  {cat.name}
                </option>
              ))}
            </select>
          </div>
          <div className="form-group">
            <label>Город</label>
            <input
              type="text"
              name="city"
              value={filters.city}
              onChange={handleFilterChange}
              placeholder="Введите город"
            />
          </div>
          <div className="form-group">
            <label>Мин. цена</label>
            <input
              type="number"
              name="min_price"
              value={filters.min_price}
              onChange={handleFilterChange}
              placeholder="0"
            />
          </div>
          <div className="form-group">
            <label>Макс. цена</label>
            <input
              type="number"
              name="max_price"
              value={filters.max_price}
              onChange={handleFilterChange}
              placeholder="1000000"
            />
          </div>
          <div className="form-group">
            <label>Поиск</label>
            <input
              type="text"
              name="search"
              value={filters.search}
              onChange={handleFilterChange}
              placeholder="Поиск по тексту"
            />
          </div>
        </div>
      </div>

      <h2>Объявления ({pagination.total})</h2>

      {ads.length === 0 ? (
        <div className="card">
          <p>Объявления не найдены</p>
        </div>
      ) : (
        <>
          <div className="grid">
            {ads.map((ad) => (
              <Link
                key={ad.id}
                to={`/ads/${ad.id}`}
                style={{ textDecoration: 'none', color: 'inherit' }}
              >
                <div className="ad-card">
                  {ad.images && ad.images.length > 0 ? (
                    <img src={getImageUrl(ad.images[0])} alt={ad.title} />
                  ) : (
                    <div style={{ height: '200px', background: '#f0f0f0', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                      Нет изображения
                    </div>
                  )}
                  <div className="ad-card-content">
                    <div className="ad-card-title">{ad.title}</div>
                    {ad.price && (
                      <div className="ad-card-price">{ad.price.toLocaleString()} ₽</div>
                    )}
                    <div className="ad-card-city">{ad.city}</div>
                    {ad.category && (
                      <div style={{ marginTop: '10px', fontSize: '12px', color: '#999' }}>
                        {ad.category.name}
                      </div>
                    )}
                  </div>
                </div>
              </Link>
            ))}
          </div>

          {pagination.last_page > 1 && (
            <div className="pagination">
              <button
                onClick={() => handlePageChange(pagination.current_page - 1)}
                disabled={pagination.current_page === 1}
              >
                Назад
              </button>
              {Array.from({ length: pagination.last_page }, (_, i) => i + 1)
                .filter(
                  (page) =>
                    page === 1 ||
                    page === pagination.last_page ||
                    (page >= pagination.current_page - 2 &&
                      page <= pagination.current_page + 2)
                )
                .map((page, index, array) => (
                  <React.Fragment key={page}>
                    {index > 0 && array[index - 1] !== page - 1 && (
                      <span>...</span>
                    )}
                    <button
                      onClick={() => handlePageChange(page)}
                      className={pagination.current_page === page ? 'active' : ''}
                    >
                      {page}
                    </button>
                  </React.Fragment>
                ))}
              <button
                onClick={() => handlePageChange(pagination.current_page + 1)}
                disabled={pagination.current_page === pagination.last_page}
              >
                Вперед
              </button>
            </div>
          )}
        </>
      )}
    </div>
  )
}

export default Home

