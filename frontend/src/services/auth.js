import { computed, reactive } from 'vue'
import { authApi } from './api'
import { resetNotifications } from '../composables/useNotifications'

const TOKEN_KEY = 'app-auth-token'
const USER_KEY = 'app-auth-user'
const SELECTED_CHILD_KEY = 'app-selected-child-id'
const SELECTED_COACH_GROUP_KEY = 'app-selected-coach-group-id'

const getStoredValue = (key) => localStorage.getItem(key) || sessionStorage.getItem(key) || ''

const getActiveStorage = () => {
  if (localStorage.getItem(TOKEN_KEY)) return localStorage
  if (sessionStorage.getItem(TOKEN_KEY)) return sessionStorage
  return localStorage
}

const parseStoredUser = () => {
  const raw = localStorage.getItem(USER_KEY) || sessionStorage.getItem(USER_KEY)

  if (!raw) return null

  try {
    return JSON.parse(raw)
  } catch {
    localStorage.removeItem(USER_KEY)
    sessionStorage.removeItem(USER_KEY)
    return null
  }
}

const state = reactive({
  token: getStoredValue(TOKEN_KEY),
  user: parseStoredUser(),
  ready: false,
  bootstrapping: null
})

const clearStoredAuth = () => {
  localStorage.removeItem(TOKEN_KEY)
  localStorage.removeItem(USER_KEY)
  sessionStorage.removeItem(TOKEN_KEY)
  sessionStorage.removeItem(USER_KEY)
}

const persistAuth = ({ remember = true } = {}) => {
  clearStoredAuth()

  const storage = remember ? localStorage : sessionStorage

  if (state.token) {
    storage.setItem(TOKEN_KEY, state.token)
  }

  if (state.user) {
    storage.setItem(USER_KEY, JSON.stringify(state.user))
  }
}

export const setAuth = ({ token, user }, options = {}) => {
  state.token = token
  state.user = user
  localStorage.removeItem(SELECTED_CHILD_KEY)
  sessionStorage.removeItem(SELECTED_CHILD_KEY)
  localStorage.removeItem(SELECTED_COACH_GROUP_KEY)
  sessionStorage.removeItem(SELECTED_COACH_GROUP_KEY)
  resetNotifications()
  persistAuth(options)
}

export const clearAuth = () => {
  state.token = ''
  state.user = null
  localStorage.removeItem(SELECTED_CHILD_KEY)
  sessionStorage.removeItem(SELECTED_CHILD_KEY)
  localStorage.removeItem(SELECTED_COACH_GROUP_KEY)
  sessionStorage.removeItem(SELECTED_COACH_GROUP_KEY)
  resetNotifications()
  clearStoredAuth()
}

export const bootstrapAuth = async () => {
  if (state.bootstrapping) return state.bootstrapping

  state.bootstrapping = (async () => {
    if (!state.token) {
      state.ready = true
      return null
    }

    try {
      const user = await authApi.me()
      state.user = user
      resetNotifications()
      persistAuth({ remember: getActiveStorage() === localStorage })
      state.ready = true
      return user
    } catch (error) {
      clearAuth()
      state.ready = true
      return null
    } finally {
      state.bootstrapping = null
    }
  })()

  return state.bootstrapping
}

export const login = async (payload) => {
  const { remember = true, ...credentials } = payload
  const result = await authApi.login(credentials)
  setAuth(result, { remember })
  return result
}

export const register = async (payload) => {
  const result = await authApi.register(payload)
  setAuth(result)
  return result
}

export const logout = async () => {
  try {
    await authApi.logout()
  } finally {
    clearAuth()
  }
}

export const useAuth = () => ({
  state,
  user: computed(() => state.user),
  token: computed(() => state.token),
  isAuthenticated: computed(() => Boolean(state.token)),
  ready: computed(() => state.ready)
})
