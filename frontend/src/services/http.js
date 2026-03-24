import axios from 'axios'

const TOKEN_KEY = 'app-auth-token'
let isHandlingUnauthorized = false

export const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  }
})

apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem(TOKEN_KEY)

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error?.response?.status === 401 && !isHandlingUnauthorized) {
      isHandlingUnauthorized = true
      localStorage.removeItem(TOKEN_KEY)
      localStorage.removeItem('app-auth-user')

      if (window.location.pathname !== '/login') {
        window.location.assign('/login')
      }
    }

    return Promise.reject(error)
  }
)
