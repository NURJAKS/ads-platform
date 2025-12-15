import React, { useEffect } from 'react'
import { useNavigate, useSearchParams } from 'react-router-dom'
import apiClient from '../api/client'

const GoogleCallback = () => {
    const [searchParams] = useSearchParams()
    const navigate = useNavigate()

    useEffect(() => {
        const token = searchParams.get('token')

        if (token) {
            localStorage.setItem('token', token)

            // Optionally fetch user data here if needed, or just redirect
            // For now, let's redirect to home
            navigate('/')
            window.location.reload() // Reload to update auth state in context
        } else {
            navigate('/login?error=google_auth_failed')
        }
    }, [searchParams, navigate])

    return (
        <div className="loading">
            Authenticating with Google...
        </div>
    )
}

export default GoogleCallback
