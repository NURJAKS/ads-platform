import React from 'react'
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider } from './context/AuthContext'
import Layout from './components/Layout'
import Home from './pages/Home'
import Login from './pages/Login'
import Register from './pages/Register'
import GoogleCallback from './pages/GoogleCallback'
import AdDetail from './pages/AdDetail'
import CreateAd from './pages/CreateAd'
import EditAd from './pages/EditAd'
import MyAds from './pages/MyAds'
import AdminPanel from './pages/AdminPanel'
import PrivateRoute from './components/PrivateRoute'
import AdminRoute from './components/AdminRoute'

function App() {
  return (
    <AuthProvider>
      <Router>
        <Layout>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            <Route path="/auth/google/callback" element={<GoogleCallback />} />
            <Route path="/ads/:id" element={<AdDetail />} />

            <Route
              path="/ads/create"
              element={
                <PrivateRoute>
                  <CreateAd />
                </PrivateRoute>
              }
            />
            <Route
              path="/ads/:id/edit"
              element={
                <PrivateRoute>
                  <EditAd />
                </PrivateRoute>
              }
            />
            <Route
              path="/my/ads"
              element={
                <PrivateRoute>
                  <MyAds />
                </PrivateRoute>
              }
            />
            <Route
              path="/admin"
              element={
                <AdminRoute>
                  <AdminPanel />
                </AdminRoute>
              }
            />

            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </Layout>
      </Router>
    </AuthProvider>
  )
}

export default App

