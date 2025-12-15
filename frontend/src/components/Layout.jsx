import React from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

const Layout = ({ children }) => {
  const { user, logout, isAdmin, isAuthenticated } = useAuth()
  const navigate = useNavigate()

  const handleLogout = async () => {
    await logout()
    navigate('/')
  }

  return (
    <div>
      <header className="header">
        <div className="container">
          <div className="header-content">
            <Link to="/" style={{ textDecoration: 'none', color: '#333' }}>
              <h1 style={{ margin: 0 }}>Платформа объявлений</h1>
            </Link>
            <nav className="header-nav">
              <Link to="/">Главная</Link>
              
              {isAuthenticated ? (
                <>
                  <Link to="/ads/create">Создать объявление</Link>
                  <Link to="/my/ads">Мои объявления</Link>
                  {isAdmin() && <Link to="/admin">Админ-панель</Link>}
                  <span style={{ color: '#666' }}>{user?.name}</span>
                  <button className="btn btn-secondary" onClick={handleLogout}>
                    Выйти
                  </button>
                </>
              ) : (
                <>
                  <Link to="/login">Войти</Link>
                  <Link to="/register">Регистрация</Link>
                </>
              )}
            </nav>
          </div>
        </div>
      </header>
      
      <main className="container" style={{ paddingTop: '20px', paddingBottom: '40px' }}>
        {children}
      </main>
    </div>
  )
}

export default Layout

