import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

const api = axios.create({
  baseURL: (import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL as string).replace(/\/$/, '') : '') + '/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: true,
})

// Request interceptor — attach token dynamically
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Response interceptor — handle 401 gracefully
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Do not clear auth or redirect if the 401 came from the login/register endpoint itself
    const url = error.config?.url || ''
    if (url.includes('/auth/login') || url.includes('/auth/register') || url.includes('/auth/forgot-password')) {
      return Promise.reject(error)
    }

    if (error.response?.status === 401) {
      const authStore = useAuthStore()
      authStore.clearAuth()
      if (router.currentRoute.value.path !== '/login') {
        router.push('/login')
      }
    }
    return Promise.reject(error)
  }
)

export default api
